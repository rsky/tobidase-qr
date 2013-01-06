<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 描画コンテキストクラス
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

namespace TobidaseQR\Image;

use TobidaseQR\Color\Table;
use Imagick;
use ImagickDraw;
use ImagickPixel;

/**
 * 描画コンテキストクラス
 */
class RenderContext
{
    /**
     * 画像サイズ定数
     */
    const WIDTH  = 32;
    const HEIGHT = 32;

    /**
     * Imagickオブジェクト
     *
     * @var Imagick
     */
    protected $imagick;

    /**
     * ImagickDrawオブジェクト
     *
     * @var ImagickDraw
     */
    protected $draw;

    /**
     * カラーテーブル
     *
     * @var ImagickPixel[]
     */
    protected $colors;

    /**
     * コンストラクタ
     *
     * @param int[] $palette
     * @param TobidaseQR\Color\Table $table
     */
    public function __construct(array $palette, Table $table = null)
    {
        if (!$table) {
            $table = new Table;
        }

        $colors = [];

        foreach ($palette as $code) {
            $colors[] = $table->getRgbPixel($code);
        }

        $this->image = new Imagick;
        $this->image->newImage(self::WIDTH, self::HEIGHT, $colors[0]);
        $this->draw = new ImagickDraw;
        $this->colors = $colors;
    }

    /**
     * 1ピクセル描画する
     *
     * @param int $x
     * @param int $y
     * @param int $color
     *
     * @return Imagick
     */
    public function drawPixel($x, $y, $color)
    {
        $this->draw->setFillColor($this->colors[$color]);
        $this->draw->point($x, $y);
    }

    /**
     * 描画結果のImagickオブジェクトを返す
     *
     * @param void
     *
     * @return Imagick
     */
    public function getImage()
    {
        $this->image->drawImage($this->draw);
        $this->draw = new ImagickDraw;

        return clone $this->image;
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
