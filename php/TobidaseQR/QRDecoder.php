<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * QRコードデコーダクラス
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

namespace TobidaseQR;

use TobidaseQR\Entity\Design;
use TobidaseQR\Entity\MyDesign;
use TobidaseQR\Entity\Player;
use TobidaseQR\Entity\Village;
use TobidaseQR\Exception\DecoderException;
use Imagick;

/**
 * QRコードデコーダクラス
 *
 * 別のプログラムで解析済の生データのデコードのみを行う
 */
class QRDecoder
{
    /**
     * コンストラクタ
     *
     * @param void
     */
    public function __construct()
    {
        $this->validator = new Validator;
    }

    /**
     * バイナリデータをデコードしてマイデザインオブジェクトを生成する
     *
     * @param string $binary
     *
     * @return TobidaseQR\Entity\MyDesign
     *
     * @throws TobidaseQR\Exception\DecoderException,
     *         TobidaseQR\Exception\ValidatorException
     */
    public function decodeBinary($binary)
    {
    }

    /**
     * 16進ダンプ文字列をデコードしてマイデザインオブジェクトを生成する
     *
     * @param string $hex
     *
     * @return TobidaseQR\Entity\MyDesign
     *
     * @throws TobidaseQR\Exception\DecoderException,
     *         TobidaseQR\Exception\ValidatorException
     */
    public function decodeHexString($hex)
    {
        $binary = pack('H*', preg_replace('/[^0-9A-Fa-f]+/', '', $hex));

        return $this->decodeBinary($binary);
    }

    /**
     * UCS-2LE文字列をUTF-8文字列に変換する
     *
     * @param string $str
     *
     * @return string
     */
    private function encodeString($str)
    {
        return mb_convert_encoding($str, 'UTF-8', 'UCS-2LE');
    }

    /**
     * カラーコードをQRコード上の表記から
     * 0から始まる連番の内部表記に変換する
     *
     * 不正なカラーコードの場合は-1を返す
     *
     * @param int $code
     *
     * @return int
     */
    private function decodeColorCode($code)
    {
        $upperBits = ($code & 0xf0) >> 4;
        $lowerBits = $code & 0xf;

        if ($lowerBits === 0xf) {
            // モノクロ15色
            if ($upperBits === 0xf) {
                return -1;
            }
            return 144 + $upperBits;
        } else {
            // カラー9x16色
            if ($lowerBits > 8) {
                return -1;
            }
            return 9 * $upperBits + $lowerBits;
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
