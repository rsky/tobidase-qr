<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 入力画像の各ピクセルにカラーコードを割り当てるクラス
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

namespace TobidaseQR\Color\Mapper;

use TobidaseQR\Color\Table;
use TobidaseQR\Color\Mapper;
use Imagick;
use InvalidArgumentException;

/**
 * 入力画像の各ピクセルにカラーコードを割り当てるクラス
 *
 * 誤差拡散法（Floyd-Steinberg法）でディザリングした結果に
 * 最も近いパレットの中の色が選ばれる
 */
class DitheringMapper implements Mapper
{
    /**
     * オプションキー
     */
    const OPTION_ALGORITHM = 'ditheringAlgorithm';

    /**
     * ディザリングアルゴリズム
     */
    const ALGO_FLOYD_STEINBERG
        = 'TobidaseQR\Image\Dithering\FloydSteinberg';
    const ALGO_FALSE_FLOYD_STEINBERG
        = 'TobidaseQR\Image\Dithering\FalseFloydSteinberg';
    const ALGO_JARVIS_JUDICE_NINKE
        = 'TobidaseQR\Image\Dithering\JarvisJudiceNinke';
    const ALGO_STUCKI      = 'TobidaseQR\Image\Dithering\Stucki';
    const ALGO_BURKES      = 'TobidaseQR\Image\Dithering\Burkes';
    const ALGO_SIERRA3     = 'TobidaseQR\Image\Dithering\Sierra3';
    const ALGO_SIERRA2     = 'TobidaseQR\Image\Dithering\Sierra2';
    const ALGO_SIERRA_2_4A = 'TobidaseQR\Image\Dithering\Sierra24A';

    /**
     * ディザリングアルゴリズムの別名
     */
    const ALGO_JAJUNI      = self::ALGO_JARVIS_JUDICE_NINKE;
    const ALGO_SIERRA      = self::ALGO_SIERRA3;
    const ALGO_SIERRA_LITE = self::ALGO_SIERRA_2_4A;
    const ALGO_DEFAULT     = self::ALGO_SIERRA3;

    /**
     * 画像の各ピクセルにカラーコードを割り当てる
     *
     * @param Imagick $image
     * @param TobidaseQR\Color\Table $table
     * @param array $options
     *
     * @return int[][] カラーコードの2次元配列
     *
     * @throws InvalidArgumentException
     */
    public function map(Imagick $image, Table $table, array $options = [])
    {
        $algorithm = (isset($options[self::OPTION_ALGORITHM]))
            ? $options[self::OPTION_ALGORITHM]
            : self::ALGO_DEFAULT;

        if (!class_exists($algorithm)
            || !is_a($algorithm, 'TobidaseQR\\Image\\DitheringAlgorithm', true)
        ) {
            throw InvalidArgumentException(
                "{$algorithm} is not a kind of DitheringAlgorithm"
            );
        }

        return (new $algorithm($table, $options))->apply($image);
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
