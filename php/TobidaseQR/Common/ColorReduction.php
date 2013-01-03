<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 減色機能を持つトレイト
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
    protected $reducer;

    /**
     * 連想配列のオプションからオブジェクトをセットする
     *
     * @param array $options
     *
     * @return void
     */
    public function setColorReductionOptions(array $options = [])
    {
        if (isset($options[OptionKey::COLOR_REDUCER])) {
            $this->setColorReducer($options[OptionKey::COLOR_REDUCER]);
        } else {
            $this->setStandardColorReducer();
        }
    }

    /**
     * 減色処理オブジェクトを返す
     *
     * @param void
     *
     * @return TobidaseQR\Color\Reducer $reduer
     */
    public function getColorReducer()
    {
        return $this->reduer;
    }

    /**
     * 減色処理オブジェクトをセットする
     *
     * @param TobidaseQR\Color\Reducer $reduer
     *
     * @return void
     */
    public function setColorReducer(Reducer $reduer)
    {
        $this->reduer = $reduer;
    }

    /**
     * 標準の減色処理オブジェクトをセットする
     *
     * @param void
     *
     * @return void
     */
    public function setStandardColorReducer()
    {
        $this->reduer = new Reducer;
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
