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

namespace TobidaseQR\QRCode;

use TobidaseQR\Entity\Design;
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
     *
     * @return QRCode
     */
    public function makeQRCode(MyDesign $myDesign)
    {
        $options = $this->options;
        if ($this->checkStructuredType($myDesign->design->type)) {
            $options['maxnum'] = 4;
        } else {
            $options['maxnum'] = 1;
        }

        $qr = new QRCode($options);
        $qr->addData($this->makeData($myDesign), QRCode::EM_8BIT);
        $qr->finalize();

        return $qr;
    }

    /**
     * QRコードの元となるデータを組み立てる
     *
     * @param MyDesign $myDesign
     *
     * @return string
     */
    public function makeData(MyDesign $myDesign)
    {
        $design = $myDesign->design;
        $data = $this->makeHeader($myDesign) . $this->makeBitmap($design);

        // 連結QRコードを生成するタイプの場合は
        // バイト数が切りの良い数字になるように
        // ゼロパディングする
        if ($this->checkStructuredType($design->type)) {
            $data .= pack('V', 0);
        }

        return $data;
    }

    /**
     * QRコードの元となるデータのヘッダ部を組み立てる
     *
     * @param MyDesign $myDesign
     *
     * @return string
     */
    public function makeHeader(MyDesign $myDesign)
    {
        $player  = $myDesign->player;
        $village = $myDesign->village;

        $myDesignName = $this->encodeString($myDesign->name);
        $playerName   = $this->encodeString($player->name);
        $villageName  = $this->encodeString($village->name);

        return pack('a40v', $myDesignName, 0)
            . pack('va18v', $player->id, $playerName, $player->number)
            . pack('va18v', $village->id, $villageName, 0)
            . pack('CC', self::MAGICK_1, self::MAGICK_2)
            . $this->makePalette($myDesign->design);
    }

    /**
     * QRコードの元となるデータのパレット部(+α)を組み立てる
     *
     * @param Design $design
     *
     * @return string
     */
    public function makePalette(Design $design)
    {
        $colors = [
            0x0f, 0x1f, 0x2f,
            0x3f, 0x4f, 0x5f,
            0x6f, 0x7f, 0x8f,
            0x9f, 0xaf, 0xbf,
            0xcf, 0xdf, 0xef,
        ];

        foreach ($design->palette as $index => $code) {
            $colors[$index] = $this->encodeColorCode($code);
        }

        // パレットに続く8bit値
        // 今のところ謎なので仮にXORハッシュを求めてみる
        // @FIXME 正確な値を出せるようにしたい
        $hash = 0x3d;
        foreach ($colors as $code) {
            $hash ^= $code;
        }
        $colors[] = $hash;

        array_unshift($colors, 'C16');

        return call_user_func_array('pack', $colors)
            . pack('CCv', self::MAGICK_A, $design->type, 0);
    }

    /**
     * QRコードの元となるデータのビットマップ部を組み立てる
     *
     * @param Design $design
     *
     * @return string
     */
    public function makeBitmap(Design $design)
    {
        $ipalette = array_flip($design->palette);
        $data = '';

        foreach ($design->bitmap as $row) {
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
     * UTF-8文字列をUCS-2LE文字列に変換する
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
     * カラーコードを0から始まる連番の内部表記から
     * QRコード用の表記に変換する
     *
     * @param int $code
     *
     * @return int
     */
    private function encodeColorCode($code)
    {
        if ($code < 144) {
            // カラー9x16色
            return ((int)($code / 9) << 4) | ($code % 9);
        } else {
            // モノクロ15色
            return (($code - 144) << 4) | 0xf;
        }
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
            Design::TYPE_DRESS_LONG_SLEEEVED,
            Design::TYPE_DRESS_SHORT_SLEEEVED,
            Design::TYPE_DRESS_NO_SLEEEVE,
            Design::TYPE_SHIRT_LONG_SLEEEVED,
            Design::TYPE_SHIRT_SHORT_SLEEEVED,
            Design::TYPE_SHIRT_NO_SLEEEVE,
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
