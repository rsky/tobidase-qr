<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * ヒストグラム作成機能を持つトレイト
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

namespace TobidaseQR\Common;

use TobidaseQR\Color\MapperInterface;
use TobidaseQR\Color\Mapper\SimpleMapper;
use TobidaseQR\Color\Table;
use Imagick;

/**
 * ヒストグラム作成機能を持つトレイト
 */
trait Histgram
{
    /**
     * カラーテーブルオブジェクト
     *
     * @var TobidaseQR\Color\Table
     */
    private $table;

    /**
     * カラーマッパーオブジェクト
     *
     * @var TobidaseQR\Color\MapperInterface
     */
    private $histgramMapper;

    /**
     * 連想配列のオプションからオブジェクトをセットする
     *
     * @param array $options
     *
     * @return void
     */
    public function setHistgramOptions(array $options = [])
    {
        if (isset($options[Option::COLOR_TABLE])) {
            $this->setColorTable($options[Option::COLOR_TABLE]);
        } else {
            $this->setColorTable(new Table);
        }

        if (isset($options[Option::HISTGRAM_MAPPER])) {
            $this->setHistgramMapper($options[Option::HISTGRAM_MAPPER]);
        } else {
            $this->setHistgramMapper(new SimpleMapper);
        }
    }

    /**
     * ヒストグラム作成用のカラーマッパーを返す
     *
     * @param void
     *
     * @return TobidaseQR\Color\MapperInterface $histgramMapper
     */
    public function getHistgramMapper()
    {
        return $this->histgramMapper;
    }

    /**
     * カラーテーブルを返す
     *
     * @param void
     *
     * @return TobidaseQR\Color\Table $table
     */
    public function getColorTable()
    {
        return $this->table;
    }

    /**
     * ヒストグラム作成用のカラーマッパーをセットする
     *
     * @param TobidaseQR\Color\MapperInterface $histgramMapper
     *
     * @return void
     */
    public function setHistgramMapper(MapperInterface $histgramMapper)
    {
        $this->histgramMapper = $histgramMapper;
    }

    /**
     * カラーテーブルをセットする
     *
     * @param TobidaseQR\Color\Table $table
     *
     * @return void
     */
    public function setColorTable(Table $table)
    {
        $this->table = $table;
    }

    /**
     * 有効なカラーコードのリストを返す
     *
     * @param void
     *
     * @return int[]
     */
    protected function getAllColorCodes()
    {
        return array_keys($this->table->getRgbColorTable());
    }

    /**
     * Imagickオブジェクトから近似色のヒストグラムを作成する
     *
     * @param Imagick $image 色空間は COLORSPACE_RGB or COLORSPACE_SRGB
     *
     * @return array
     *
     * @see TobidaseQR\Color\Table::createHistgram()
     */
    protected function createHistgram(Imagick $image)
    {
        return $this->table->createHistgram($image, $this->histgramMapper);
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
