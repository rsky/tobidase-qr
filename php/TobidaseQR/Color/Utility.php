<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 色ユーティリティクラス
 *
 * とびだせ どうぶつの森は任天堂(株)の登録商標です
 * QRコードは(株)デンソーウェーブの登録商標です
 *
 * Copyright (c) 2012-2013 Ryusuke SEKIYAMA <rsky0711@gmail.com>,
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
 * @copyright   2012-2013 Ryusuke SEKIYAMA <rsky0711@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * 色空間の変換には下記URLを参考にさせていただきました
 * @link http://w3.kcua.ac.jp/~fujiwara/infosci/colorspace/colorspace3.html
 */

namespace TobidaseQR\Color;

/**
 * 色ユーティリティクラス
 */
class Utility
{
    /**
     * D65光源のRGB値をD50光源XYZ値へ変換した後に
     * 値を[0.0..1.0]に正規化するための白色点の値
     */
    const D65_D50_WHITEPOINT_X = 0.9642;
    const D65_D50_WHITEPOINT_Y = 1.0;
    const D65_D50_WHITEPOINT_Z = 0.8249;

    /**
     * sRGB(D65)からXYZ(D50)への変換行列の値
     */
    const SRGB_MATRIX_R_X = 0.436041;
    const SRGB_MATRIX_R_Y = 0.222485;
    const SRGB_MATRIX_R_Z = 0.013920;
    const SRGB_MATRIX_G_X = 0.385113;
    const SRGB_MATRIX_G_Y = 0.716905;
    const SRGB_MATRIX_G_Z = 0.097067;
    const SRGB_MATRIX_B_X = 0.143046;
    const SRGB_MATRIX_B_Y = 0.060610;
    const SRGB_MATRIX_B_Z = 0.713913;

    /**
     * XYZをL*a*b*に変換する際の定数
     */
    const XYZ2LAB_THRESHOLD = 0.00885645; // pow(6 / 29, 3)
    const XYZ2LAB_FACTOR    = 903.296;    // pow(29 / 3, 3)

    /**
     * XYZ→L*a*b*変換で各要素の値を変換する関数
     *
     * @param float $t
     *
     * @return float
     */
    private static function xyzToLabConversion($t)
    {
        if ($t > self::XYZ2LAB_THRESHOLD) {
            return pow($t, 1.0 / 3.0);
        } else {
            return (self::XYZ2LAB_FACTOR * $t + 16.0) / 116.0;
        }
    }

    /**
     * CIE XYZ表色系をCIE L*a*b*表色系に変換する
     *
     * @param float $x X成分 [0.0..1.0]
     * @param float $y Y成分 [0.0..1.0]
     * @param float $z Z成分 [0.0..1.0]
     *
     * @return float[] ($L, $a, $b)
     */
    public static function xyzToLab($x, $y, $z)
    {
        $fx = self::xyzToLabConversion($z);
        $fy = self::xyzToLabConversion($y);
        $fz = self::xyzToLabConversion($z);

        return [
            116.0 * $fy - 16.0,
            500.0 * ($fx - $fy),
            200.0 * ($fy - $fz),
        ];
    }

    /**
     * CIE RGB表色系をCIE L*a*b*表色系に変換する
     *
     * RGBの色空間はsRGBで光源はD65、
     * L*a*b*の光源はD50として変換を行う
     *
     * @param int $r 赤色成分 [0..255]
     * @param int $g 緑色成分 [0..255]
     * @param int $b 青色成分 [0..255]
     *
     * @return float[] ($L, $a, $b)
     */
    public static function rgbToLab($r, $g, $b)
    {
        $x  = $r / 255.0 * self::SRGB_MATRIX_R_X
            + $g / 255.0 * self::SRGB_MATRIX_G_X
            + $b / 255.0 * self::SRGB_MATRIX_B_X;

        $y  = $r / 255.0 * self::SRGB_MATRIX_R_Y
            + $g / 255.0 * self::SRGB_MATRIX_G_Y
            + $b / 255.0 * self::SRGB_MATRIX_B_Y;

        $z  = $r / 255.0 * self::SRGB_MATRIX_R_Z
            + $g / 255.0 * self::SRGB_MATRIX_G_Z
            + $b / 255.0 * self::SRGB_MATRIX_B_Z;

        return self::xyzToLab(
            $x / self::D65_D50_WHITEPOINT_X,
            $y / self::D65_D50_WHITEPOINT_Y,
            $z / self::D65_D50_WHITEPOINT_Z
        );
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
