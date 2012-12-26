<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * QRコードエンコーダクラス
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

use QRCode;

/**
 * QRコードエンコーダクラス
 */
class QREncoder
{
    /**
     * QRコード作成オプションの既定値
     *
     * @var array
     */
    private $defaultOptions = [
        'eclevel'   => QRCode::ECL_M,
        'masktype'  => 4,
        'format'    => QRCode::FMT_PNG,
        'magnify'   => 3,
        'separator' => 6,
    ];

    /**
     * QRコード作成オプション
     *
     * @var array
     */
    private $options;

    /**
     * コンストラクタ
     *
     * @param array $options QRコード作成オプション
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->defaultOptions, $options);
    }

    /**
     * QRコードオブジェクトを作成する
     *
     * @param MyDesign $myDesign
     * @param Player $player
     * @param Village $village
     *
     * @return QRCode
     */
    public function makeQRCode(
        MyDesign $myDesign,
        Player $player,
        Village $village
    ) {
        $data = $this->composeData($myDesign, $player, $village);
        $qr = new QRCode($this->options);
        $qr->addData($data, QRCode::EM_8BIT);
        $qr->finalize();

        return $qr;
    }

    /**
     * QRコードの元となるデータを組み立てる
     *
     * @param MyDesign $myDesign
     * @param Player $player
     * @param Village $village
     *
     * @return string
     */
    public function composeData(
        MyDesign $myDesign,
        Player $player,
        Village $village
    ) {
        $dName = $this->encodeString($myDesign->getName());
        $pName = $this->encodeString($player->name);
        $vName = $this->encodeString($village->name);

        $data = pack('a40v', $dName, 0)
            . pack('va18v', $player->id, $pName, $player->number)
            . pack('va18v', $village->id, $vName, 0)
            . pack('CC', 1, 2);

        $palette = $myDesign->getPalette();
        $ipalette = array_flip($palette);
        $colors = array_fill(0, 15, 0xf);

        foreach ($palette as $index => $code) {
            if ($code < 144) {
                $colors[$index] = ((int)($code / 9) << 4) | ($code % 9);
            } else {
                $colors[$index] = (($code - 144) << 4) | 0xf;
            }
        }

        array_unshift($colors, 'C15');
        $data .= call_user_func_array('pack', $colors);
        $data .= pack('CCCv', 0x31, 10, 9, 0);
        $body = ['C*'];
        $offset = 0;

        foreach ($myDesign->getData() as $row) {
            foreach ($row as $code) {
                if ($offset % 2) {
                    $body[] = $value | ($ipalette[$code] << 4);
                } else {
                    $value = $ipalette[$code];
                }
                $offset++;
            }
        }

        $data .= call_user_func_array('pack', $body);

        return $data;
    }

    /**
     * UTF-8文字列をUCS-2LEに変換する
     *
     * @param string $str
     *
     * @return string
     */
    private function encodeString($str)
    {
        return mb_convert_encoding($str, 'UCS-2LE', 'UTF-8');
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
