<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 画像読み込み・変換クラス
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

use TobidaseQR\Color\Table;
use TobidaseQR\Color\Reducer;
use TobidaseQR\Color\Mapper;
use Imagick;
use UnexpectedValueException;

/**
 * 画像読み込み・変換クラス
 */
class Loader
{
    /**
     * オプションキー
     */
    const OPTION_MAP    = 'map';
    const OPTION_REDUCE = 'reduce';

    /**
     * 幅
     *
     * @var int
     */
    private $width;

    /**
     * 高さ
     *
     * @var int
     */
    private $height;

    /**
     * コンストラクタ
     *
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * 画像を読み込む
     *
     * @param Imagick $image
     * @param array $options
     *
     * @return array ($palette, $data)
     */
    public function load(Imagick $image, array $options = [])
    {
        $this->correctColorSpace($image);
        $this->resize($image);

        $reduceOptions = [];
        $mapOptions = [];

        foreach ([self::OPTION_MAP, self::OPTION_REDUCE] as $optKey) {
            if (array_key_exists($optKey, $options)) {
                $optValue = $options[$optKey];
                if (!is_array($optValue)) {
                    throw new UnexpectedValueException(
                        "\$options['{$optKey}'] must be an array"
                    );
                }
                ${$optKey . 'Options'} = $optValue;
            }
        }

        $table = new Table;
        $histgram = $table->createHistgram($image, $reduceOptions);

        $reducer = new Reducer($table->getRgbColorTable(), $reduceOptions);
        $reducedTable = $reducer->reduceColor($histgram);

        if (!empty($mapOptions[Mapper::OPTION_DITHERING])) {
            $mapper = new Mapper\DitheringMapper;
        } else {
            $mapper = new Mapper\SimpleMapper;
        }

        $rows = $mapper->map($image, $reducedTable, $mapOptions);

        return [$reducedTable->getRgbColorTable(), $rows];
    }

    /**
     * 画像の色空間をRGBにする
     *
     * @param Imagick $image
     *
     * @return void
     */
    public function correctColorSpace(Imagick $image)
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
     *
     * @return void
     */
    public function resize(Imagick $image)
    {
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();

        // 小さい画像を引き伸ばす
        if ($width < $this->width || $height < $this->height) {
            if ($width / $height > $this->width / $this->height) {
                // 横長画像→縦を合わせ、横ははみ出す
                $tmpWidth = round($this->height * $width / $height);
                $tmpHeight = $this->height;
                $ratio = $tmpHeight / $height;
            } else {
                // 縦長画像→横を合わせ、縦ははみ出す
                $tmpHeight = round($this->width * $height / $width);
                $tmpWidth = $this->width;
                $ratio = $tmpWidth / $width;
            }

            if (0.75 < $ratio && $ratio < 1.25) {
                $image->adaptiveResizeImage($tmpWidth, $tmpHeight, true);
            } else {
                $image->resizeImage(
                    $tmpWidth, $tmpHeight, Imagick::FILTER_LANCZOS, 1.0, true
                );
            }

            $width = $tmpWidth;
            $height = $tmpHeight;
        }

        // はみ出る部分をカットする
        if ($width > $this->width) {
            $image->cropImage(
                $this->width, $height, floor(($width - $this->width) / 2), 0
            );
        }
        if ($height > $this->height) {
            $image->cropImage(
                $width, $this->height, 0, floor(($height - $this->height) / 2)
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
