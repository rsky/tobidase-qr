<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 色範囲クラス
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
 * 色範囲クラス
 */
class ColorRange
{
    /**
     * 各チャンネルの最大値/最小値/差分
     *
     * @var int
     */
    private $maxR;
    private $minR;
    private $deltaR;
    private $maxG;
    private $minG;
    private $deltaG;
    private $maxB;
    private $minB;
    private $deltaB;

    /**
     * 差分の最大値
     *
     * @var int
     */
    private $deltaMax;

    /**
     * 内包する色
     *
     * @param ColorRangeElement[]
     */
    private $colors;

    /**
     * コンストラクタ
     *
     * @param ColorRangeElement[] $colors
     */
    public function __construct(array $colors)
    {
        $this->colors = $colors;

        $r = [];
        $g = [];
        $b = [];

        foreach ($colors as $color) {
            $r[] = $color->r;
            $g[] = $color->g;
            $b[] = $color->b;
        }

        $this->maxR = max($r);
        $this->minR = min($r);
        $this->maxG = max($g);
        $this->minG = min($g);
        $this->maxB = max($b);
        $this->minB = min($b);
        $this->deltaR = $this->maxR - $this->minR;
        $this->deltaG = $this->maxG - $this->minG;
        $this->deltaB = $this->maxB - $this->minB;
        $this->deltaMax = max($this->deltaR, $this->deltaG, $this->deltaB);
    }

    /**
     * 各チャンネルの最大値と最小値の差のうち最も大きいものを返す
     *
     * @param void
     *
     * @return int
     */
    public function getMaxChannelBandWidth()
    {
        return $this->deltaMax;
    }

    /**
     * 範囲を分割可能か判定する
     *
     * @param void
     *
     * @return bool
     */
    public function canSplit()
    {
        if (count($this->colors) < 2) {
            return false;
        }

        return $this->deltaMax > 0;
    }

    /**
     * 範囲をR/G/Bのチャンネルのうち最大値と最小値の差が
     * もっとも大きいものの中央値で分割する
     *
     * @param void
     *
     * @return TobidaseQR\ColorRange[]
     */
    public function splitByMedian()
    {
        if ($this->deltaMax === $this->deltaG) {
            $targetChannel = 'g';
            $max = $this->maxG;
            $min = $this->minG;
        } elseif ($this->deltaMax === $this->deltaR) {
            $targetChannel = 'r';
            $max = $this->maxR;
            $min = $this->minR;
        } else {
            $targetChannel = 'b';
            $max = $this->maxB;
            $min = $this->minB;
        }

        $median = ($max + $min) / 2;
        $uppers = [];
        $lowers = [];

        foreach ($this->colors as $code => $color) {
            $value = $color->$targetChannel;
            if ($min <= $value && $value < $median) {
                $lowers[$code] = $color;
            } elseif ($median <= $value && $value <= $max) {
                $uppers[$code] = $color;
            } else {
                throw new RuntimeException("Unexpected color: {$color->code}");
            }
        }

        if (count($uppers) === 0) {
            throw new RuntimeException('Upper range is empty');
        }
        if (count($lowers) === 0) {
            throw new RuntimeException('Lower range is empty');
        }

        return [new ColorRange($uppers), new ColorRange($lowers)];
    }

    /**
     * 最も出現頻度の高いカラーコードを返す
     *
     * @param void
     *
     * @return TobidaseQR\ColorRangeElement
     */
    public function getMostSignificantColor()
    {
        uasort($this->colors, function ($a, $b) {
            if ($a->frequency === $b->frequency) {
                return $b->cmpValue - $a->cmpValue;
            } else {
                return $b->frequency - $a->frequency;
            }
        });

        return reset($this->colors);
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
