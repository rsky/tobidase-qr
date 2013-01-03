<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 画像読み込みクラス
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

namespace TobidaseQR\Image;

use TobidaseQR\Common\ColorMapping;
use Imagick;

/**
 * 画像読み込みクラス
 */
class Loader
{
    use ColorMapping;

    /**
     * オプションキー
     */
    const OPTION_RESIZE_FILTER = 'resizeFilter';
    const OPTION_RESIZE_BLUR   = 'resizeBlur';
    const OPTION_COLOR_MAPPER  = 'colorMapper';
    const OPTION_COLOR_TABLE   = 'colorTable';

    /**
     * リサイズで使う窓関数の既定値
     */
    const DEFAULT_RESIZE_FILTER = Imagick::FILTER_LANCZOS;

    /**
     * リサイズのぼけ具合の既定値
     */
    const DEFAULT_RESIZE_BLUR = 0.75;

    /**
     * リサイズで使う窓関数
     *
     * @var int
     */
    private $filter;

    /**
     * リサイズのぼけ具合
     *
     * @var float
     */
    private $blur;

    /**
     * コンストラクタ
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->filter = (isset($options[self::OPTION_RESIZE_FILTER]))
            ? (int)$options[self::OPTION_RESIZE_FILTER]
            : self::DEFAULT_RESIZE_FILTER;

        $this->blur = (isset($options[self::OPTION_RESIZE_BLUR]))
            ? (float)$options[self::OPTION_RESIZE_BLUR]
            : self::DEFAULT_RESIZE_BLUR;

        if (isset($options[self::OPTION_COLOR_MAPPER])) {
            $this->setColorMapper($options[self::OPTION_COLOR_MAPPER]);
        } else {
            $this->setStandardColorMapper();
        }

        if (isset($options[self::OPTION_COLOR_TABLE])) {
            $this->setColorTable($options[self::OPTION_COLOR_TABLE]);
        } else {
            $this->setStandardColorTable();
        }
    }

    /**
     * 画像を読み込む
     *
     * @param mixed $source Imagickオブジェクトもしくは画像ファイルのパス
     * @param int $width
     * @param int $height
     *
     * @return Imagick
     */
    public function loadImage($source, $width, $height)
    {
        if ($source instanceof Imagick) {
            $image = clone $source;
        } else {
            $image = new Imagick($source);
        }

        $this->correctImageColorSpace($image);
        $this->resizeImage($image, $width, $height);

        return $image;
    }

    /**
     * 画像を読み込み、カラーテーブルの色を割り当てたビットマップを返す
     *
     * @param mixed $source Imagickオブジェクトもしくは画像ファイルのパス
     * @param int $width
     * @param int $height
     *
     * @return int[][]
     */
    public function loadImageAsBitmap($source, $width, $height)
    {
        $image = $this->loadImage($bitmap, $width, $height);

        return $this->mapper->map($image, $this->table);
    }

    /**
     * 画像の色空間をRGBにする
     *
     * @param Imagick $image
     *
     * @return void
     */
    public function correctImageColorSpace(Imagick $image)
    {
        $cs = $image->getColorspace();
        if ($cs !== Imagick::COLORSPACE_RGB
            && $cs !== Imagick::COLORSPACE_SRGB
        ) {
            $image->setColorspace(Imagick::COLORSPACE_RGB);
        }
    }

    /**
     * 画像をリサイズする
     *
     * @param Imagick $image
     * @param int $width
     * @param int $height
     *
     * @return void
     */
    public function resizeImage(Imagick $image, $width, $height)
    {
        $srcWidth = $image->getImageWidth();
        $srcHeight = $image->getImageHeight();

        // 小さい画像を引き伸ばす
        if ($srcWidth < $width || $srcHeight < $height) {
            if ($srcWidth / $srcHeight > $width / $height) {
                // 横長画像→縦を合わせ、横ははみ出す
                $tmpWidth = round($height * $srcWidth / $srcHeight);
                $tmpHeight = $height;
                $ratio = $tmpHeight / $srcHeight;
            } else {
                // 縦長画像→横を合わせ、縦ははみ出す
                $tmpHeight = round($width * $srcHeight / $srcWidth);
                $tmpWidth = $width;
                $ratio = $tmpWidth / $srcWidth;
            }

            if (0.875 < $ratio && $ratio < 1.125) {
                $image->adaptiveResizeImage($tmpWidth, $tmpHeight, true);
            } else {
                $image->resizeImage(
                    $tmpWidth, $tmpHeight, $this->filter, $this->blur, true
                );
            }

            $srcWidth = $tmpWidth;
            $srcHeight = $tmpHeight;
        }

        // はみ出る部分をカットする
        if ($srcWidth > $width) {
            $image->cropImage(
                $width, $srcHeight, floor(($srcWidth - $width) / 2), 0
            );
        }
        if ($srcHeight > $height) {
            $image->cropImage(
                $srcWidth, $height, 0, floor(($srcHeight - $height) / 2)
            );
        }
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
