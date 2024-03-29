<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 画像の読み込み機能を持つトレイト
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

namespace TobidaseQR\Common;

use TobidaseQR\Image\Loader;

/**
 * 画像の読み込み機能を持つトレイト
 */
trait ImageLoading
{
    /**
     * 画像ローダーオブジェクト
     *
     * @var TobidaseQR\Image\Loader
     */
    private $loader;

    /**
     * 連想配列のオプションからオブジェクトをセットする
     *
     * @param array $options
     *
     * @return void
     */
    public function setImageLoadingOptions(array $options = [])
    {
        if (isset($options[Option::IMAGE_LOADER])) {
            $this->setImageLoader($options[Option::IMAGE_LOADER]);
        } else {
            $this->setImageLoader(new Loader);
        }
    }

    /**
     * 画像ローダーを返す
     *
     * @param void
     *
     * @return TobidaseQR\Image\Loader $loader
     */
    public function getImageLoader()
    {
        return $this->loader;
    }

    /**
     * 画像ローダーをセットする
     *
     * @param TobidaseQR\Image\Loader $loader
     *
     * @return void
     */
    public function setImageLoader(Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * 標準の画像ローダーをセットする
     *
     * @param mixed $image
     * @param int $width
     * @param int $height
     *
     * @return Imagick
     *
     * @see TobidaseQR\Image\Loader::loadImage()
     */
    protected function loadImage($image, $width, $height)
    {
        return $this->loader->loadImage($image, $width, $height);
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
