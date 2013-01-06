<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * RGB色情報クラス
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
 */

namespace TobidaseQR\Color;

use ImagickPixel;

/**
 * RGB色情報クラス
 */
class RGBColor
{
    /**
     * カラーコード
     *
     * @var int
     */
    public $code;

    /**
     * 各チャンネルの値
     *
     * @var int
     */
    public $r;
    public $g;
    public $b;

    /**
     * 元画像での出現頻度
     *
     * @var int
     */
    public $frequency;

    /**
     * 比較用の値
     *
     * @var int
     */
    public $cmpValue;

    /**
     * コンストラクタ
     *
     * @param int $code
     * @param array $rgb
     * @param int $frequency
     */
    public function __construct($code, array $rgb, $frequency = 0)
    {
        list($r, $g, $b) = $rgb;
        $this->code = $code;
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
        $this->frequency = $frequency;
        $this->cmpValue = ($g << 16) | ($r << 8) | $b;
    }

    /**
     * RGB値からImagickPixelオブジェクトを作成する
     *
     * @param void
     *
     * @return ImagickPixel
     */
    public function toImagickPixel()
    {
        return new ImagickPixel(
            sprintf('#%02x%02x%02x', $this->r, $this->g, $this->b)
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
