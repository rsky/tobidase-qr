<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 減色機能を持つトレイト
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

use TobidaseQR\Color\Reducer;

/**
 * 減色機能を持つトレイト
 */
trait ColorReduction
{
    /**
     * 減色処理オブジェクト
     *
     * @var TobidaseQR\Color\Reducer
     */
    private $reducer;

    /**
     * 減色済カラーテーブル
     *
     * @var TobidaseQR\Color\Table
     */
    private $reducedTable;

    /**
     * 最後に与えられたヒストグラムを正規化したもののJSON値
     * 減色済カラーテーブルを再利用する判定に使う
     *
     * @var string
     */
    private $histgramJson;

    /**
     * 連想配列のオプションからオブジェクトをセットする
     *
     * @param array $options
     *
     * @return void
     */
    public function setColorReductionOptions(array $options = [])
    {
        if (isset($options[Option::COLOR_REDUCER])) {
            $this->setColorReducer($options[Option::COLOR_REDUCER]);
        } else {
            $this->setColorReducer(new Reducer);
        }
    }

    /**
     * 減色処理オブジェクトを返す
     *
     * @param void
     *
     * @return TobidaseQR\Color\Reducer $reducer
     */
    public function getColorReducer()
    {
        return $this->reducer;
    }

    /**
     * 減色処理オブジェクトをセットする
     *
     * @param TobidaseQR\Color\Reducer $reducer
     *
     * @return void
     */
    public function setColorReducer(Reducer $reducer)
    {
        $this->reducer = $reducer;
    }

    /**
     * ヒストグラムを元に減色を行う
     *
     * @param array $histgram
     *
     * @return array
     */
    protected function reduceColor(array $histgram)
    {
        $histgram = array_filter($histgram);
        ksort($histgram, SORT_NUMERIC);
        $json = json_encode($histgram, JSON_FORCE_OBJECT);
        if ($this->reducedTable && $json === $this->histgramJson) {
            return $this->reducedTable->getRgbColorTable();
        }

        $this->reducedTable = $this->reducer->reduceColor($histgram);
        $this->histgramJson = $json;

        return $this->reducedTable->getRgbColorTable();
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
