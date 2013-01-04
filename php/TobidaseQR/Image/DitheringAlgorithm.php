<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 入力画像をディザリングするインターフェイス
 *
 * とびだせ どうぶつの森は任天堂(株)の登録商標です
 * QRコードは(株)デンソーウェーブの登録商標です
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

namespace TobidaseQR\Image;

use TobidaseQR\Color\Table;
use Imagick;

/**
 * 入力画像をディザリングするインターフェイス
 */
interface DitheringAlgorithm
{
    /**
     * ディザリングアルゴリズム名
     */
    const FLOYD_STEINBERG       = 'FloydSteinberg';
    const FALSE_FLOYD_STEINBERG = 'FalseFloydSteinberg';
    const JARVIS_JUDICE_NINKE   = 'JarvisJudiceNinke';
    const STUCKI      = 'Stucki';
    const BURKES      = 'Burkes';
    const SIERRA3     = 'Sierra3';
    const SIERRA2     = 'Sierra2';
    const SIERRA_2_4A = 'Sierra24A';

    /**
     * ディザリングアルゴリズムの別名
     */
    const JAJUNI      = self::JARVIS_JUDICE_NINKE;
    const SIERRA      = self::SIERRA3;
    const SIERRA_LITE = self::SIERRA_2_4A;

    /**
     * コンストラクタ
     *
     * @param array $options
     */
    public function __construct(array $options = []);

    /**
     * 画像にディザリングを適用する
     *
     * @param Imagick $image
     * @param TobidaseQR\Color\Table $table
     *
     * @return int[][] カラーコードの2次元配列
     */
    public function apply(Imagick $image, Table $table);
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
