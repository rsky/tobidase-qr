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
     * データサイズ定数
     */
    const DATA_SIZE_SINGLE  = 620;  // 108 + 512
    const DATA_SIZE_QUAD    = 2160; // 108 + 2048 + 4
    const HEADER_SIZE       = 108;
    const BODY_SIZE_SINGLE  = 512;  // 32 / 2 * 32
    const BODY_SIZE_QUAD    = 2048; // 512 * 4

    /**
     * ヘッダ内フィールドのオフセット定数
     */
    const MYDESIGN_NAME_OFFSET  = 0;
    const PLAYER_ID_OFFSET      = 0x2a;
    const PLAYER_NAME_OFFSET    = 0x2c;
    const PLAYER_NUMBER_OFFSET  = 0x3e;
    const VILLAGE_ID_OFFSET     = 0x40;
    const VILLAGE_NAME_OFFSET   = 0x42;
    const DESIGN_PALETTE_OFFSET = 0x58;
    const DESIGN_TYPE_OFFSET    = 0x69;

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
     * @param string $data
     *
     * @return TobidaseQR\Entity\MyDesign
     *
     * @throws TobidaseQR\Exception\DecoderException,
     *         UnexpectedValueException
     */
    private function decodeStruct($data)
    {
        $size = strlen($data);
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
            $terminator = bin2hex(substr($data, -4));
            if ($terminator !== '00000000') {
                throw new DecoderException(
                    "Invalid terminator '{$terminator}'",
                    DecoderException::WRONG_STRUCT
                );
            }
        }

        $myDesign = $this->decodeHeader($data, $quad);
        $bitmap = $this->decodeBitmap($data, $quad, self::HEADER_SIZE);
        $myDesign->design->bitmap = $bitmap;

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
     * @throws TobidaseQR\Exception\DecoderException,
     *         UnexpectedValueException
     */
    public function decodeHeader($data, $quad)
    {
        $myDesign = new MyDesign;
        $myDesign->name    = null;
        $myDesign->player  = new Player;
        $myDesign->village = new Village;
        $myDesign->design  = new Design;
        $headerExtra = new HeaderExtra;

        $format = implode('/', [
            'A24' . 'myDesignName',
            'A18' . 'myDesignNamePadding',
            'v'   . 'playerId',
            'A12' . 'playerName',
            'A6'  . 'playerNamePadding',
            'v'   . 'playerNumber',
            'v'   . 'villageId',
            'A12' . 'villageName',
            'A8'  . 'villageNamePadding',
            'C2'  . 'magicNumber',
            'A15' . 'palette',
            'C'   . 'paletteExtra',
            'C'   . 'magicNumberA',
            'C'   . 'designType',
            'v'   . 'terminator',
        ]);

        $values = unpack($format, $data);
        if (!$values) {
            throw new DecoderException('Failed to parse header');
        }

        // デコードと検証が必要な値を処理
        try {
            $this->offset = 0;

            $offset = self::MYDESIGN_NAME_OFFSET;
            $name = $this->decodeMyDesignName($values['myDesignName']);
            $myDesign->name = $name;

            $offset = self::PLAYER_NAME_OFFSET;
            $name = $this->decodePlayerName($values['playerName']);
            $myDesign->player->name = $name;

            $offset = self::VILLAGE_NAME_OFFSET;
            $name = $this->decodeVillageName($values['villageName']);
            $myDesign->village->name = $name;

            $offset = self::DESIGN_PALETTE_OFFSET;
            $palette = $this->decodePalette($values['palette']);
            $myDesign->design->palette = $palette;

            $offset = self::DESIGN_TYPE_OFFSET;
            $type = $values['designType'];
            $this->validator->validateDesignType($type, $quad);
            $myDesign->design->type = $type;
        } catch (UnexpectedValueException $e) {
            $this->offset += $offset;
            throw $e;
        }

        // マイデザイン
        $padding = bin2hex($values['myDesignNamePadding']);
        $headerExtra->myDesignNamePadding = $padding;

        // プレイヤー情報
        $myDesign->player->id = $values['playerId'];
        $padding = bin2hex($values['playerNamePadding']);
        $headerExtra->playerNamePadding = $padding;
        $myDesign->player->number = $values['playerNumber'];

        // 村情報
        $myDesign->village->id = $values['villageId'];
        $padding = bin2hex($values['villageNamePadding']);
        $headerExtra->villageNamePadding = $padding;

        // その他のヘッダフィールド
        $headerExtra->magicNumber1 = $values['magicNumber1'];
        $headerExtra->magicNumber2 = $values['magicNumber2'];
        $headerExtra->paletteExtra = $values['paletteExtra'];
        $headerExtra->magicNumberA = $values['magicNumberA'];
        $headerExtra->terminator   = $values['terminator'];

        $myDesign->headerExtra = $headerExtra;

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
        $name = $this->decodeString($name);
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
        $name = $this->decodeString($name);
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
        $name = $this->decodeString($name);
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
     * @throws TobidaseQR\Exception\DecoderException,
     *         UnexpectedValueException
     */
    public function decodePalette($data)
    {
        $rawPalette = array_values(unpack('C15', $data));
        if (!$rawPalette) {
            throw new DecoderException(
                'Failed to decode palette',
                DecoderException::UNEXPECTED_SEQUENCE
            );
        }

        $palette = [];

        foreach ($rawPalette as $offset => $value) {
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
     * @param string $data
     * @param bool $quad
     * @param int $offset
     *
     * @return int[][]
     *
     * @throws TobidaseQR\Exception\DecoderException,
     *         UnexpectedValueException
     */
    public function decodeBitmap($data, $quad, $offset = 0)
    {
        $rowCount = ($quad) ? 108 : 32;
        $bitmap = [];

        for ($rowno = 0; $rowno < $rowCount; $rowno++) {
            $rowBytes = unpack('C16', substr($data, $offset, 16));
            if (!$rowBytes) {
                $this->offset = $offset;
                throw new DecoderException(
                    "Failed to decode bitmap at row {$rowno}",
                    DecoderException::UNEXPECTED_SEQUENCE
                );
            }

            $row = [];
            $colno = 0;

            foreach ($rowBytes as $byte) {
                $upper = ($byte & 0xf0) >> 4;
                $lower = $byte & 0xf;

                if ($upper === 0xf || $lower === 0xf) {
                    $this->offset = $offset;
                    throw new UnexpectedValueException(sprintf(
                        'Unexpected byte 0x%02x found', $byte
                    ), DecoderException::UNEXPECTED_SEQUENCE);
                }

                $row[] = $lower;
                $row[] = $upper;
                $colno++;
            }

            $bitmap[] = $row;
            $offset += 16;
        }

        return $bitmap;
    }

    /**
     * UCS-2LE文字列をUTF-8文字列に変換する
     *
     * デコード結果末尾のヌルバイトは消去して返す
     *
     * @param string $str
     *
     * @return string
     */
    private function decodeString($str)
    {
        return rtrim(mb_convert_encoding($str, 'UTF-8', 'UCS-2LE'), "\0");
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
