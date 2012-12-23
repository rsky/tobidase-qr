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

namespace TobidaseQR;

/**
 * 減色処理クラス
 */
class ColorReducer
{
    /**
     * 減色する色数
     *
     * @const int
     */
    const DEFAULT_TARGET_COLOR_COUNT = 15;

    /**
     * カラーテーブル
     *
     * @var array
     */
    private $table;

    /**
     * 色範囲のリスト
     *
     * @var TobidaseQR\ColorRange[]
     */
    private $ranges;

    /**
     * コンストラクタ
     *
     * @param array $rgbTable
     */
    public function __construct(array $rgbTable = null)
    {
        if ($rgbTable) {
            $this->table = $rgbTable;
        } else {
            $this->table = (new ColorTable)->getRgbColorTable();
        }
    }

    /**
     * ヒストグラムを元に減色処理を行ったカラーテーブルを返す
     *
     * @param array $histgram
     * @param int $count
     *
     * @return array
     */
    public function reduceColor(
        array $histgram,
        $count = self::DEFAULT_TARGET_COLOR_COUNT
    ) {
        if (count($histgram) <= $count) {
            return $this->createReducedColorTable(array_keys($histgram));
        }

        // STAGE 1: 最もよく使われる色をパレットに抽出する
        $palette = $this->filterTopColors($histgram, floor($count / 2));
        $remains = array_slice(array_keys($histgram), count($palette));
        $splitLimit = $count - count($palette);

        // STAGE 2: 残りの色から作成した範囲オブジェクトを
        // メディアンカット法で必要な数まで分割する
        $colors = [];
        foreach ($remains as $code) {
            $colors[$code] = new Color(
                $code, $this->table[$code], $histgram[$code]
            );
        }

        $this->ranges = [new ColorRange($colors)];
        while (count($this->ranges) < $splitLimit && $this->canSplitRange()) {
            $this->splitRange();
        }

        // STAGE 3: 各範囲から最も出現頻度の高い色を抽出し、それらを
        // 出現頻度でソートしてからカラーコードをパレットに追加する
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
     * @param array $histgram
     * @param int $limit
     *
     * @return int[]
     */
    public function filterTopColors(array $histgram, $limit = -1)
    {
        $median = (max($histgram) + min($histgram)) / 2;

        arsort($histgram, SORT_NUMERIC);

        $topColors = [];
        foreach ($histgram as $code => $frequency) {
            if ($frequency > $median) {
                $topColors[] = $code;
                if ($limit > 0 && count($topColors) > $limit) {
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
     * @return TobidaseQR\ColorTable
     */
    public function createReducedColorTable(array $palette)
    {
        $reducedTable = [];

        foreach ($palette as $code) {
            $reducedTable[$code] = $this->table[$code];
        }

        return new ColorTable($reducedTable);
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
