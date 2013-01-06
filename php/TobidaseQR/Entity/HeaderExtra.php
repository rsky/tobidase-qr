<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * その他のヘッダ情報エンティティクラス
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

namespace TobidaseQR\Entity;

use TobidaseQR\Common\JSONSerializable;
use TobidaseQR\Common\JSONSerialization;

/**
 * その他のヘッダ情報エンティティクラス
 */
class HeaderExtra implements JSONSerializable
{
    use JSONSerialization;

    /**
     * マイデザイン名の後に続くデータの16進表記
     *
     * 期待値は "000000000000000000000000000000000000"
     *
     * @var string
     */
    public $myDesignNamePadding;

    /**
     * プレイヤー名の後に続くデータの16進表記
     *
     * 期待値は "000000000000"
     *
     * @var string
     */
    public $playerNamePadding;

    /**
     * 村名の後に続くデータの16進表記
     *
     * 期待値は "0000000000000000"
     *
     * @var string
     */
    public $villageNamePadding;

    /**
     * パレットの前にある8bitのマジックナンバーその1
     *
     * 期待値は 1 (0x01)
     *
     * @var int
     */
    public $magicNumber1;

    /**
     * パレットの前にある8bitのマジックナンバーその2
     *
     * 期待値は 2 (0x02)
     *
     * @var string
     */
    public $magicNumber2;

    /**
     * パレットの後にある8bitの値
     *
     * @var int
     */
    public $paletteExtra;

    /**
     * パレットの後にある8bitのマジックナンバー
     *
     * 期待値は 10 (0x0a)
     *
     * @var int
     */
    public $magicNumberA;

    /**
     * ヘッダの最後にある16bitの終端記号
     *
     * 期待値は 0 (0x0000)
     *
     * @var int
     */
    public $terminator;
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
