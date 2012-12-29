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
use TobidaseQR\Entity\HeaderExtra;
use TobidaseQR\Entity\MyDesign;
use TobidaseQR\Entity\Player;
use TobidaseQR\Entity\Village;
use TobidaseQR\Exception\DecoderException;
use Imagick;
use UnexpectedValueException;

/**
 * QRコードデコーダクラス
 *
 * 別のプログラムで解析済の生データのデコードのみを行う
 */
class QRDecoder
{
    /**
     * 全体のデータサイズ定数
     */
    const DATA_SIZE_SINGLE  = 620;  // 108 + 512
    const DATA_SIZE_QUAD    = 2160; // 108 + 512 * 4 + 4

    /**
     * オフセット定数
     */
    const MYDESIGN_NAME_OFFSET  = 0;
    const PLAYER_ID_OFFSET      = 0x2a;
    const PLAYER_NAME_OFFSET    = 0x2c;
    const PLAYER_NUMBER_OFFSET  = 0x3e;
    const VILLAGE_ID_OFFSET     = 0x40;
    const VILLAGE_NAME_OFFSET   = 0x42;
    const DESIGN_PALETTE_OFFSET = 0x58;
    const DESIGN_BITMAP_OFFSET  = 0x6c;

    /**
     * データ長定数
     */
    const MYDESIGN_NAME_LENGTH  = 24;
    const PLAYER_ID_LENGTH      = 2;
    const PLAYER_NAME_LENGTH    = 12;
    const PLAYER_NUMBER_LENGTH  = 2;
    const VILLAGE_ID_LENGTH     = 2;
    const VILLAGE_NAME_LENGTH   = 12;
    const DESIGN_PALETTE_LENGTH = 15;
    const DESIGN_BITMAP_LENGTH  = 512;

    /**
     * 不正なカラーコードを示す値
     */
    const INVALID_COLOR_CODE = -1;

    /**
     * 解析中のマイデザインオブジェクト
     *
     * @var TobidaseQR\Entity\MyDesign
     */
    private $myDesign;

    /**
     * 例外情報に含めるエラー発生位置
     *
     * @var int
     */
    private $offset;

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
     * データをデコードしてマイデザインオブジェクトを生成する
     *
     * @param string $binary
     *
     * @return TobidaseQR\Entity\MyDesign
     *
     * @throws TobidaseQR\Exception\DecoderException
     */
    public function decode($data)
    {
        if (strpos($data, "\0") === false) {
            return $this->decodeHexString($data);
        } else {
            return $this->decodeBinary($data);
        }
    }

    /**
     * バイナリデータをデコードしてマイデザインオブジェクトを生成する
     *
     * @param string $binary
     *
     * @return TobidaseQR\Entity\MyDesign
     *
     * @throws TobidaseQR\Exception\DecoderException
     */
    public function decodeBinary($binary)
    {
        try {
            return $this->decodeStruct($binary);
        } catch (DecoderException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DecoderException(
                sprintf(
                    '%s [%d]: %s near offset %d',
                    get_class($e),
                    $e->getCode(),
                    $e->getMessage(),
                    $this->offset
                ), DecoderException::UNEXPECTED_VALUE, $e
            );
        }
    }

    /**
     * 16進表記の文字列をデコードしてマイデザインオブジェクトを生成する
     *
     * @param string $hex
     *
     * @return TobidaseQR\Entity\MyDesign
     *
     * @throws TobidaseQR\Exception\DecoderException
     */
    public function decodeHexString($hex)
    {
        if (preg_match('/[^0-9A-Fa-f\\s]/u', $hex, $matches, PREG_OFFSET_CAPTURE)) {
            $char = bin2hex($matches[0][0]);
            $offset = $matches[0][1];
            throw new DecoderException(
                "Invalid sequence '{$char}' found at offset {$offset}",
                DecoderException::WRONG_INPUT
            );
        }

        $hex = preg_replace('/\\s+/u', '', $hex);
        $length = strlen($hex);
        if ($length !== self::DATA_SIZE_SINGLE * 2
            && $length !== self::DATA_SIZE_QUAD * 2
        ) {
            throw new DecoderException(
                "Invalid hex length ({$length})",
                DecoderException::WRONG_INPUT
            );
        }

        return $this->decodeBinary(pack('H*', $hex));
    }

    /**
     * バイナリデータをデコードしてマイデザインオブジェクトを生成する
     *
     * @param string $binary
     *
     * @return TobidaseQR\Entity\MyDesign
     *
     * @throws TobidaseQR\Exception\DecoderException,
     *         UnexpectedValueException
     */
    private function decodeStruct($binary)
    {
        $size = strlen($binary);
        $quad = false;

        if ($size === self::DATA_SIZE_QUAD) {
            $quad = true;
        } elseif ($size !== self::DATA_SIZE_SINGLE) {
            throw new DecoderException(
                "Invalid data size ({$size})",
                DecoderException::WRONG_INPUT
            );
        }

        if ($quad) {
            $terminator = bin2hex(substr($binary, -4));
            if ($terminator !== '00000000') {
                throw new DecoderException(
                    "Invalid terminator '{$terminator}'",
                    DecoderException::WRONG_STRUCT
                );
            }
        }

        $header = substr($binary, 0, self::DESIGN_BITMAP_OFFSET);
        $myDesign = $this->decodeHeader($header, $quad);

        try {
            $bitmap = substr($binary, self::DESIGN_BITMAP_OFFSET);
            $myDesign->design->bitmap = $this->decodeBitmap($bitmap, $quad);
        } catch (UnexpectedValueException $e) {
            $this->offset += self::DESIGN_BITMAP_OFFSET;
            throw $e;
        }

        return $myDesign;
    }

    /**
     * ヘッダ部をデコードする
     *
     * @param string $header
     * @param bool $quad
     *
     * @return TobidaseQR\Entity\MyDesign
     *
     * @throws UnexpectedValueException
     */
    public function decodeHeader($header, $quad)
    {
        $myDesign = new MyDesign;
        $myDesign->name    = null;
        $myDesign->player  = new Player;
        $myDesign->village = new Village;
        $myDesign->design  = new Design;
        $myDesign->headerExtra = new HeaderExtra;

        try {
            $this->offset = 0;

            $offset = self::MYDESIGN_NAME_OFFSET;
            $length = self::MYDESIGN_NAME_LENGTH;
            $myDesign->name = $this->decodeMyDesignName(
                substr($header, $offset, $length)
            );

            $offset = self::MYDESIGN_NAME_OFFSET + self::MYDESIGN_NAME_LENGTH;
            $length = self::PLAYER_ID_OFFSET - $offset;
            $myDesign->headerExtra->myDesignNamePadding
                = bin2hex(substr($header, $offset, $length));

            $offset = self::PLAYER_ID_OFFSET;
            $length = self::PLAYER_ID_LENGTH;
            $myDesign->player->id = unpack('vshort', substr(
                $header, $offset, $length
            ))['short'];

            $offset = self::PLAYER_NAME_OFFSET;
            $length = self::PLAYER_NAME_LENGTH;
            $myDesign->player->name = $this->decodePlayerName(
                substr($header, $offset, $length)
            );

            $offset = self::PLAYER_NAME_OFFSET + self::PLAYER_NAME_LENGTH;
            $length = self::PLAYER_NUMBER_OFFSET - $offset;
            $myDesign->headerExtra->playerNamePadding
                = bin2hex(substr($header, $offset, $length));

            $offset = self::PLAYER_NUMBER_OFFSET;
            $length = self::PLAYER_NUMBER_LENGTH;
            $myDesign->player->number = unpack('vshort', substr(
                $header, $offset, $length
            ))['short'];

            $offset = self::VILLAGE_ID_OFFSET;
            $length = self::VILLAGE_ID_LENGTH;
            $myDesign->village->id = unpack('vshort', substr(
                $header, $offset, $length
            ))['short'];

            $offset = self::VILLAGE_NAME_OFFSET;
            $length = self::VILLAGE_NAME_LENGTH;
            $myDesign->village->name = $this->decodeVillageName(
                substr($header, $offset, $length)
            );

            $offset = self::VILLAGE_NAME_OFFSET + self::VILLAGE_NAME_LENGTH;
            $length = self::DESIGN_PALETTE_OFFSET - $offset;
            $padding = substr($header, $offset, $length);
            $numbers = unpack('C2byte', substr($padding, -2));
            $myDesign->headerExtra->villageNamePadding
                = bin2hex(substr($padding, 0, -2));
            $myDesign->headerExtra->magickNumber1 = $numbers['byte1'];
            $myDesign->headerExtra->magickNumber2 = $numbers['byte2'];

            $offset = self::DESIGN_PALETTE_OFFSET;
            $length = self::DESIGN_PALETTE_LENGTH;
            $myDesign->design->palette = $this->decodePalette(
                substr($header, $offset, $length)
            );

            $offset = self::DESIGN_PALETTE_OFFSET + self::DESIGN_PALETTE_LENGTH;
            $length = self::DESIGN_BITMAP_OFFSET - $offset;
            $padding = substr($header, $offset, $length);
            $numbers = unpack('C3byte/vshort', substr($header, $offset, $length));
            $myDesign->headerExtra->paletteExtra = $numbers['byte1'];
            $myDesign->headerExtra->magickNumberA = $numbers['byte2'];
            $myDesign->headerExtra->headerTerminator = $numbers['short'];

            $offset += 2;
            $type = $numbers['byte3'];
            $this->validator->validateDesignType($type, $quad);
            $myDesign->design->type = $type;
        } catch (UnexpectedValueException $e) {
            $this->offset += $offset;
            throw $e;
        }

        return $myDesign;
    }

    /**
     * マイデザイン名をデコードする
     *
     * @param string $name
     *
     * @return string
     *
     * @throws UnexpectedValueException
     */
    public function decodeMyDesignName($name)
    {
        $name = rtrim($this->decodeString($name), "\0");
        $this->validator->validateMyDesignName($name);

        return $name;
    }

    /**
     * プレイヤー名をデコードする
     *
     * @param string $name
     *
     * @return string
     *
     * @throws UnexpectedValueException
     */
    public function decodePlayerName($name)
    {
        $name = rtrim($this->decodeString($name), "\0");
        $this->validator->validatePlayerName($name);

        return $name;
    }

    /**
     * 村名をデコードする
     *
     * @param string $name
     *
     * @return string
     *
     * @throws UnexpectedValueException
     */
    public function decodeVillageName($name)
    {
        $name = rtrim($this->decodeString($name), "\0");
        $this->validator->validateVillageName($name);

        return $name;
    }

    /**
     * パレットをデコードする
     *
     * @param string $data
     *
     * @return int[]
     *
     * @throws UnexpectedValueException
     */
    public function decodePalette($data)
    {
        $palette = [];

        foreach (array_values(unpack('C15', $data)) as $offset => $value) {
            $code = $this->decodeColorCode($value);
            if ($code === self::INVALID_COLOR_CODE) {
                $this->offset = $offset;
                throw new UnexpectedValueException(sprintf(
                    'Invalid color code 0x%02x', $value
                ), DecoderException::UNEXPECTED_VALUE);
            }
            $palette[] = $code;
        }

        return $palette;
    }

    /**
     * ビットマップ部をデコードする
     *
     * @param string $bitmap
     * @param bool $quad
     *
     * @return int[][]
     *
     * @throws UnexpectedValueException
     */
    public function decodeBitmap($bitmap, $quad)
    {
        $rowCount = ($quad) ? 108 : 32;
        $rows = [];

        for ($rowno = 0; $rowno < $rowCount; $rowno++) {
            $offset = 16 * $rowno;
            $rowBytes = array_values(unpack('C16', substr($bitmap, $offset, 16)));
            $row = [];

            foreach ($rowBytes as $colno => $byte) {
                $upper = ($byte & 0xf0) >> 4;
                $lower = $byte & 0xf;

                if ($upper === 0xf || $lower === 0xf) {
                    $this->offset = $offset + (int)($colno / 2);
                    throw new UnexpectedValueException(sprintf(
                        'Unexpected byte 0x%02x found', $byte
                    ), DecoderException::UNEXPECTED_SEQUENCE);
                }

                $row[] = $lower;
                $row[] = $upper;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * UCS-2LE文字列をUTF-8文字列に変換する
     *
     * @param string $str
     *
     * @return string
     */
    private function decodeString($str)
    {
        return mb_convert_encoding($str, 'UTF-8', 'UCS-2LE');
    }

    /**
     * カラーコードをQRコード上の表記から
     * 0から始まる連番の内部表記に変換する
     *
     * @param int $code
     *
     * @return int 不正な値のときは-1を返す
     *
     * @throws UnexpectedValueException
     */
    private function decodeColorCode($code)
    {
        $upperBits = ($code & 0xf0) >> 4;
        $lowerBits = $code & 0xf;

        if ($lowerBits === 0xf) {
            // モノクロ15色
            if ($upperBits === 0xf) {
                return self::INVALID_COLOR_CODE;
            }
            return 144 + $upperBits;
        } else {
            // カラー9x16色
            if ($lowerBits > 8) {
                return self::INVALID_COLOR_CODE;
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
