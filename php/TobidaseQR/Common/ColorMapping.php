<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 色の割り当て機能を持つトレイト
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

namespace TobidaseQR\Common;

use TobidaseQR\Color\MapperInterface;
use TobidaseQR\Color\Mapper\SimpleMapper;
use TobidaseQR\Color\Table;

/**
 * 色の割り当て機能を持つトレイト
 */
trait ColorMapping
{
    /**
     * カラーマッパーオブジェクト
     *
     * @var TobidaseQR\Color\MapperInterface
     */
    protected $mapper;

    /**
     * カラーテーブルオブジェクト
     *
     * @var TobidaseQR\Color\Table
     */
    protected $table;

    /**
     * 連想配列のオプションからオブジェクトをセットする
     *
     * @param array $options
     *
     * @return void
     */
    public function setColorMappingOptions(array $options = [])
    {
        if (isset($options[OptionKey::COLOR_MAPPER])) {
            $this->setColorMapper($options[OptionKey::COLOR_MAPPER]);
        } else {
            $this->setStandardColorMapper();
        }

        if (isset($options[OptionKey::COLOR_TABLE])) {
            $this->setColorTable($options[OptionKey::COLOR_TABLE]);
        } else {
            $this->setStandardColorReducer();
        }
    }

    /**
     * カラーマッパーを返す
     *
     * @param void
     *
     * @return TobidaseQR\Color\MapperInterface $mapper
     */
    public function getColorMapper()
    {
        return $this->mapper;
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
     * カラーマッパーをセットする
     *
     * @param TobidaseQR\Color\MapperInterface $mapper
     *
     * @return void
     */
    public function setColorMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
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
     * 標準のカラーマッパーをセットする
     *
     * @param void
     *
     * @return void
     */
    public function setStandardColorMapper()
    {
        $this->mapper = new SimpleMapper;
    }

    /**
     * 標準のカラーテーブルをセットする
     *
     * @param TobidaseQR\Color\Table $table
     *
     * @return void
     */
    public function setStandardColorTable()
    {
        $this->table = new Table;
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
