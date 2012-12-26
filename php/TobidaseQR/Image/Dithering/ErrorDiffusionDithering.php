<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 誤差拡散法によるディザリングの基底抽象クラス
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

namespace TobidaseQR\Image\Dithering;

use TobidaseQR\Image\DitheringAlgorithm;
use TobidaseQR\Color\Table;
use Imagick;

/**
 * 誤差拡散法によるディザリングの基底抽象クラス
 */
abstract class ErrorDiffusionDithering implements DitheringAlgorithm
{
    /**
     * オプションキー
     */
    const OPTION_DIFFUSION_WEIGHT = 'diffusionWeight';
    const OPTION_WEAVING_SCAN = 'weavingScan';

    /**
     * 誤差拡散係数に掛ける重みのデフォルト値
     *
     * サイズが小さい画像に誤差拡散法の係数を
     * そのまま使うと良い結果が得られないので補正する
     */
    const DEFAULT_DIFFUSION_WEIGHT = 0.25;

    /**
     * ディザリング対象画像
     *
     * @var Imagick
     */
    protected $image;

    /**
     * 使用するカラーテーブル
     *
     * @var TobidaseQR\Color\Table
     */
    protected $colorTable;

    /**
     * 誤差テーブル
     *
     * @var array
     */
    protected $errorTable;

    /**
     * 誤差拡散法のパラメータ
     *
     * @var array
     */
    protected $params;

    /**
     * 誤差の補正係数
     *
     * @var float
     */
    protected $weight;

    /**
     * ジグザグに走査するか否か
     *
     * @var bool
     */
    protected $weaving;

    /**
     * コンストラクタ
     *
     * @param TobidaseQR\Color\Table $table
     * @param array $options
     */
    public function __construct(Table $table, array $options = [])
    {
        $this->colorTable = $table;
        $this->weaving = (isset($options[self::OPTION_WEAVING_SCAN]))
            ? (bool)$options[self::OPTION_WEAVING_SCAN]
            : true;
        $this->weight = (isset($options[self::OPTION_DIFFUSION_WEIGHT]))
            ? (float)$options[self::OPTION_DIFFUSION_WEIGHT]
            : self::DEFAULT_DIFFUSION_WEIGHT;
    }

    /**
     * 内部状態を初期化する
     *
     * @param int $width
     * @param int $height
     *
     * @return void
     */
    abstract protected function init($width, $height);

    /**
     * 誤差を伝播する相対位置の配列と誤差に掛ける係数の配列から
     * 誤差拡散法のパラメータを組み立てる
     *
     * @param int[][] $offsets
     * @param float[] $factors
     *
     * @return array[] ((int $x, int $y, float $factor))
     */
    protected function makeParams(array $offsets, array $factors)
    {
        $divisor = array_sum($factors) / $this->weight;
        $params = [];

        foreach ($offsets as $index => $offset) {
            list($x, $y) = $offset;
            $params[] = [$x, $y, $factors[$index] / $divisor];
        }

        return $params;
    }

    /**
     * 画像に誤差拡散法によるディザリングを適用する
     *
     * @param Imagick $image
     *
     * @return int[][] カラーコードの2次元配列
     */
    public function apply(Imagick $image)
    {
        $this->image = $image;
        $width  = $image->getImageWidth();
        $height = $image->getImageHeight();
        $this->init($width, $height);
        $rows = [];

        if ($this->weaving) {
            for ($y = 0; $y < $height; $y++) {
                $rows[] = $this->scanLR($width, $y);
            }
        } else {
            for ($y = 0; $y < $height; $y++) {
                if ($y % 2) {
                    $rows[] = $this->scanRL($width, $y);
                } else {
                    $rows[] = $this->scanLR($width, $y);
                }
            }
        }

        $this->errorTable = null;

        return $rows;
    }

    /**
     * 行を左から右に走査する
     *
     * @param int $width
     * @param int $y
     *
     * @return int[]
     */
    protected function scanLR($width, $y)
    {
        $image = $this->image;
        $table = $this->colorTable;
        $params = $this->params;
        $palette = $table->getRgbColorTable();
        $row = [];

        for ($x = 0; $x < $width; $x++) {
            $col = $image->getImagePixelColor($x, $y)->getColor();
            list($errR, $errG, $errB) = $this->errorTable[$y][$x];
            $r = (int)($col['r'] + $errR);
            $g = (int)($col['g'] + $errG);
            $b = (int)($col['b'] + $errB);

            $code = $table->nearestColorCodeByRgb($r, $g, $b);
            $row[] = $code;

            list($paletteR, $paletteG, $paletteB) = $palette[$code];
            $errR = (float)($col['r'] - $paletteR);
            $errG = (float)($col['g'] - $paletteG);
            $errB = (float)($col['b'] - $paletteB);

            foreach ($params as $param) {
                $dx = $x + $param[0];
                $dy = $y + $param[1];
                $factor  = $param[2];
                $this->errorTable[$dy][$dx][0] += $errR * $factor;
                $this->errorTable[$dy][$dx][1] += $errG * $factor;
                $this->errorTable[$dy][$dx][2] += $errB * $factor;
            }
        }

        return $row;
    }

    /**
     * 行を右から左に走査する
     *
     * @param int $width
     * @param int $y
     *
     * @return int[]
     */
    protected function scanRL($width, $y)
    {
        $image = $this->image;
        $table = $this->colorTable;
        $params = $this->params;
        $palette = $table->getRgbColorTable();
        $row = [];

        list($pr, $pb, $pg) = $palette[$code];
        $dr = $pc['r'] - $pr;
        $dg = $pc['g'] - $pg;
        $db = $pc['b'] - $pb;

        for ($z = $width; $z > 0; $z--) {
            $x = $z - 1;
            $col = $image->getImagePixelColor($x, $y)->getColor();
            list($errR, $errG, $errB) = $this->errorTable[$y][$x];
            $r = (int)($col['r'] + $errR);
            $g = (int)($col['g'] + $errG);
            $b = (int)($col['b'] + $errB);

            $code = $table->nearestColorCodeByRgb($r, $g, $b);
            $row[] = $code;

            list($paletteR, $paletteG, $paletteB) = $palette[$code];
            $errR = (float)($col['r'] - $paletteR);
            $errG = (float)($col['g'] - $paletteG);
            $errB = (float)($col['b'] - $paletteB);

            foreach ($params as $param) {
                $dx = $x - $param[0];
                $dy = $y + $param[1];
                $factor  = $param[2];
                $this->errorTable[$dy][$dx][0] += $errR * $factor;
                $this->errorTable[$dy][$dx][1] += $errG * $factor;
                $this->errorTable[$dy][$dx][2] += $errB * $factor;
            }
        }

        return $row;
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
