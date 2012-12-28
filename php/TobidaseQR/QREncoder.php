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

use TobidaseQR\Entity\MyDesign;
use TobidaseQR\Entity\Player;
use TobidaseQR\Entity\Village;
use QRCode;

/**
 * QRコードエンコーダクラス
 */
class QREncoder
{
    /**
     * マジックナンバー定数
     *
     * 本当は意味があるかもしれないが、未解析の値を
     * とりあえずマジックナンバー扱いにしている
     */
    const MAGICK_1 = 0x1;
    const MAGICK_2 = 0x2;
    const MAGICK_A = 0xa;

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
        if ($this->checkStructuredType($myDesign->getType())) {
            $options['maxnum'] = 4;
        } else {
            $options['maxnum'] = 1;
        }

        $data = $this->makeData($myDesign, $player, $village);
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
    public function makeData(
        MyDesign $myDesign,
        Player $player,
        Village $village
    ) {
        $data = $this->makeHeader($myDesign, $player, $village)
            . $this->makePalette($myDesign)
            . $this->makeBitmap($myDesign);

        // 連結QRコードを生成するタイプの場合は
        // バイト数が切りの良い数字になるように
        // ゼロパディングする
        if ($this->checkStructuredType($myDesign->getType())) {
            $data .= pack('V', 0);
        }

        return $data;
    }

    /**
     * QRコードの元となるデータのヘッダ部を組み立てる
     *
     * @param MyDesign $myDesign
     * @param Player $player
     * @param Village $village
     *
     * @return string
     */
    public function makeHeader(
        MyDesign $myDesign,
        Player $player,
        Village $village
    ) {
        $dName = $this->encodeString($myDesign->getName());
        $pName = $this->encodeString($player->getName());
        $vName = $this->encodeString($village->getName());

        return pack('a40v', $dName, 0)
            . pack('va18v', $player->getId(), $pName, $player->getNumber())
            . pack('va18v', $village->getId(), $vName, 0)
            . pack('CC', self::MAGICK_1, self::MAGICK_2);
    }

    /**
     * QRコードの元となるデータのパレット部(+α)を組み立てる
     *
     * @param MyDesign $myDesign
     *
     * @return string
     */
    public function makePalette(MyDesign $myDesign)
    {
        $colors = [
            0x0f, 0x1f, 0x2f,
            0x3f, 0x4f, 0x5f,
            0x6f, 0x7f, 0x8f,
            0x9f, 0xaf, 0xbf,
            0xcf, 0xdf, 0xef,
        ];

        foreach ($myDesign->getPalette() as $index => $code) {
            if ($code < 144) {
                $colors[$index] = ((int)($code / 9) << 4) | ($code % 9);
            } else {
                $colors[$index] = (($code - 144) << 4) | 0xf;
            }
        }

        // パレットに続く8bit値
        // 今のところ謎なので仮にxorハッシュを求めてみる
        $hash = 0x3d;
        foreach ($colors as $code) {
            $hash ^= $code;
        }
        $colors[] = $hash;

        array_unshift($colors, 'C16');

        return call_user_func_array('pack', $colors)
            . pack('CCv', self::MAGICK_A, $myDesign->getType(), 0);
    }

    /**
     * QRコードの元となるデータのビットマップ部を組み立てる
     *
     * @param MyDesign $myDesign
     *
     * @return string
     */
    public function makeBitmap(MyDesign $myDesign)
    {
        $ipalette = array_flip($myDesign->getPalette());
        $data = '';

        foreach ($myDesign->getData() as $row) {
            $pack = ['C*'];
            $value = 0;
            $colno = 0;

            foreach ($row as $code) {
                if ($colno % 2 === 0) {
                    $value = $ipalette[$code];
                } else {
                    $pack[] = $value | ($ipalette[$code] << 4);
                }
                $colno++;
            }

            $data .= call_user_func_array('pack', $pack);
        }

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

    /**
     * 連結QRコードを生成するタイプか判定する
     *
     * @param int $type
     *
     * @return bool
     */
    private function checkStructuredType($type)
    {
        return in_array($type, [
            MyDesign::TYPE_DRESS_LONG_SLEEEVED,
            MyDesign::TYPE_DRESS_SHORT_SLEEEVED,
            MyDesign::TYPE_DRESS_NO_SLEEEVE,
            MyDesign::TYPE_SHIRT_LONG_SLEEEVED,
            MyDesign::TYPE_SHIRT_SHORT_SLEEEVED,
            MyDesign::TYPE_SHIRT_NO_SLEEEVE,
        ]);
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
