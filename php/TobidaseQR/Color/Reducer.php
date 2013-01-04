<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 減色処理クラス
 *
 * 「とびだせ どうぶつの森」は任天堂株式会社の登録商標です
 *
 * Copyright (c) 2012 Ryusuke SEKIYAMA <rsky0711@gmail.com>,
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package     TobidaseQR
 * @copyright   2012 Ryusuke SEKIYAMA <rsky0711@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php  MIT License
 */

namespace TobidaseQR\Color;

use TobidaseQR\Common\Option;
use InvalidArgumentException;
use OverflowException;

/**
 * 減色処理クラス
 */
class Reducer
{
    /**
     * パレット色数の既定値
     */
    const DEFAULT_PALETTE_COUNT = 15;

    /**
     * パレット色数
     *
     * @var int
     */
    private $paletteCount;

    /**
     * 必ずパレットに含める色番号のリスト
     *
     * @var int[]
     */
    private $keyColors;

    /**
     * カラーテーブル
     *
     * @var array
     */
    private $table;

    /**
     * 色範囲のリスト
     *
     * @var TobidaseQR\Color\Range[]
     */
    private $ranges;

    /**
     * コンストラクタ
     *
     * @param TobidaseQR\Color\Table $Table
     * @param array $options
     */
    public function __construct(Table $table = null, array $options = [])
    {
        $this->table = $table ?: new Table;
        $this->parseOptions($options);
    }

    /**
     * 減色オプションを解析する
     *
     * @param void
     *
     * @return void
     *
     * @throws InvalidArgumentException, OverflowException
     */
    protected function parseOptions(array $options)
    {
        $paletteCount = self::DEFAULT_PALETTE_COUNT;
        if (array_key_exists(Option::PALETTE_COUNT, $options)) {
            $paletteCount = (int)$options[Option::PALETTE_COUNT];
            if ($paletteCount < 1) {
                throw new InvalidArgumentException(
                    'paletteCount must be a positive integer'
                );
            }
        }
        $this->paletteCount = $paletteCount;

        $this->keyColors = [];
        if (array_key_exists(Option::KEY_COLOR, $options)) {
            $keyColors = (array)$options[Option::KEY_COLOR];
            if (count($keyColors) > $this->paletteCount) {
                throw new OverflowException(
                    'Too many key colors was given',
                    $e->getCode(), $e
                );
            }

            try {
                foreach ($keyColors as $keyColor) {
                    $this->table->checkColorCode($keyColor);
                    $this->keyColors[] = (int)$keyColor;
                }
            } catch (\Exception $e) {
                throw new InvalidArgumentException(
                    'specified keyColor is not available',
                    $e->getCode(), $e
                );
            }
        }
    }

    /**
     * ヒストグラムを元に減色処理を行ったカラーテーブルを返す
     *
     * @param array $histgram
     *
     * @return array
     */
    public function reduceColor(array $histgram)
    {
        $paletteCount = $this->paletteCount;
        $keyColors = array_reverse(array_unique($this->keyColors));
        $histgram = array_filter($histgram);
        arsort($histgram, SORT_NUMERIC);

        // STAGE 0: 減色の必要がなければそのまま存在する色だけを使う
        foreach ($keyColors as $keyColor) {
            unset($histgram[$keyColor]);
        }
        $palette = array_keys($histgram);
        foreach ($keyColors as $keyColor) {
            array_unshift($palette, $keyColor);
        }
        if (count($palette) <= $paletteCount) {
            return $this->createReducedColorTable($palette);
        }

        // STAGE 1: 最もよく使われる色を抽出する
        $palette = $this->filterTopColors($histgram);
        $remains = array_slice(array_keys($histgram), count($palette));
        foreach ($keyColors as $keyColor) {
            array_unshift($palette, $keyColor);
        }
        $splitLimit = $paletteCount - count($palette);
        if ($splitLimit <= 0) {
            $palette = array_slice($palette, 0, $paletteCount);

            return $this->createReducedColorTable($palette);
        }

        // STAGE 2: 残りの色から作成した範囲オブジェクトのリストを
        // メディアンカット法で必要な数まで分割する
        $colors = [];
        foreach ($remains as $code) {
            $color = $this->table->getRgbColor($code);
            $color->frequency = $histgram[$code];
            $colors[$code] = $color;
        }

        $this->ranges = [new Range($colors)];
        while (count($this->ranges) < $splitLimit && $this->canSplitRange()) {
            $this->splitRange();
        }

        // STAGE 3: 各範囲から最も出現頻度の高い色を抽出し
        // それらのカラーコードをパレットに追加する
        $colors = [];
        foreach ($this->ranges as $range) {
            $colors[] = $range->getMostSignificantColor();
        }

        usort($colors, function ($a, $b) {
            if ($a->frequency === $b->frequency) {
                return $a->code - $b->code;
            } else {
                return $b->frequency - $a->frequency;
            }
        });

        foreach ($colors as $color) {
            $palette[] = $color->code;
        }

        return $this->createReducedColorTable($palette);
    }

    /**
     * ヒストグラムから中央値を超える値のカラーコードを抽出する
     *
     * @param array $histgram 出現頻度でソート済のヒストグラム
     *
     * @return int[]
     */
    protected function filterTopColors(array $histgram)
    {
        $limit = max(1, (int)($this->paletteCount / 2));
        $median = (max($histgram) + min($histgram)) / 2;

        $topColors = [];
        $count = 0;

        foreach ($histgram as $code => $frequency) {
            if ($frequency > $median) {
                $topColors[] = $code;
                $count++;
                if ($count === $limit) {
                    break;
                }
            } else {
                break;
            }
        }

        return $topColors;
    }

    /**
     * 内包している色範囲に分割できるものがあるか判定する
     *
     * @param void
     *
     * @return bool
     */
    protected function canSplitRange()
    {
        foreach ($this->ranges as $range) {
            if ($range->canSplit()) {
                return true;
            }
        }

        return false;
    }

    /**
     * 内包している色範囲のうち、最も最大値と最小値が大きいものを分割する
     *
     * @param void
     *
     * @return void
     */
    protected function splitRange()
    {
        $targetIndex = -1;
        $currentDelta = 0;

        foreach ($this->ranges as $index => $range) {
            if ($range->canSplit()) {
                $delta = $range->getMaxChannelBandWidth();
                if ($delta > $currentDelta) {
                    $targetIndex = $index;
                    $currentDelta = $delta;
                }
            }
        }

        if ($targetIndex !== -1) {
            list($upper, $lower) = $this->ranges[$targetIndex]->splitByMedian();
            $this->ranges[$targetIndex] = $upper;
            $this->ranges[] = $lower;
        }
    }

    /**
     * 減色済カラーパレットのカラーコードとRGB値の対応表を返す
     *
     * @param int[] $palette
     *
     * @return TobidaseQR\Color\Table
     */
    public function createReducedColorTable(array $palette)
    {
        $reducedTable = [];

        foreach ($palette as $code) {
            $reducedTable[$code] = $this->table->getRgbComponents($code);
        }

        return new Table($reducedTable);
    }
}

/*
 * Local Variables:
 * mode: php
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
// vim: set filetype=php fileencoding=utf-8 expandtab tabstop=4 shiftwidth=4:
