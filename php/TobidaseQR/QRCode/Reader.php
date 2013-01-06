<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * QRコード読み込みクラス
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

namespace TobidaseQR\QRCode;

use TobidaseQR\Exception\ReaderException;

/**
 * QRコード読み込みクラス
 */
class Reader
{
    /**
     * 期待値定数
     */
    const EXPECTED_VERSION_SINGLE = 19;
    const EXPECTED_VERSION_QUAD   = 18;
    const EXPECTED_SIZE_SINGLE = 627;
    const EXPECTED_SIZE_QUAD   = 563;
    const MODE_STRUCTURED_APPEND = 3;
    const MODE_8BIT = 4;

    /**
     * QRコードデコーダ
     *
     * @var TobidaseQR\QRCode\Decoder
     */
    private $decoder;

    /**
     * コンストラクタ
     *
     * @param void
     */
    public function __construct()
    {
        $this->decoder = new Decoder;
    }

    /**
     * QRコードファイルからマイデザインのヘッダを読み込む
     *
     * @param string $filename
     *
     * @return TobidaseQR\Entity\MyDesign
     *
     * @throws TobidaseQR\Exception\ReaderException,
     *         TobidaseQR\Exception\DecoderException
     */
    public function readHeader($filename)
    {
        $result = $this->read($filename);
        $mode = $result['mode'];
        $bytes = $result['bytes'];

        if ($mode === self::MODE_8BIT) {
            $hex = bin2hex(substr($bytes, 2, Decoder::HEADER_SIZE + 2));
            $header = pack('H*', substr($hex, 1, Decoder::HEADER_SIZE * 2));
            $quad = false;
        } else {
            $header = substr($bytes, 5, Decoder::HEADER_SIZE);
            $quad = true;
        }

        return $this->decoder->decodeHeader($header, $quad);
    }

    /**
     * QRコードファイルからマイデザインを読み込む
     *
     * @param string $filename
     *
     * @return TobidaseQR\Entity\MyDesign
     *
     * @throws TobidaseQR\Exception\ReaderException,
     *         TobidaseQR\Exception\DecoderException
     */
    public function readSingle($filename)
    {
        $result = $this->read($filename);
        if ($result['mode'] !== self::MODE_8BIT) {
            throw new ReaderException(
                "Unexpected content mode: {$result['mode']}",
                ReaderException::UNEXPECTED_VALUE
            );
        }

        $hex = bin2hex(substr($result['bytes'], 2));
        $hex = substr($hex, 1, Decoder::DATA_SIZE_SINGLE * 2);

        return $this->decoder->decodeHexString($hex);
    }

    /**
     * PROデザインを書き出した4つのQRコードファイルからマイデザインを読み込む
     *
     * @param string $file1
     * @param string $file2
     * @param string $file3
     * @param string $file4
     *
     * @return TobidaseQR\Entity\MyDesign
     *
     * @throws TobidaseQR\Exception\ReaderException,
     *         TobidaseQR\Exception\DecoderException
     */
    public function readQuad($file1, $file2, $file3, $file4)
    {
        $symbols = [];

        foreach ([$file1, $file2, $file3, $file4] as $filename) {
            $result = $this->read($filename);
            if ($result['mode'] !== self::MODE_STRUCTURED_APPEND) {
                throw new ReaderException(
                    "Unexpected content mode: {$result['mode']}",
                    ReaderException::UNEXPECTED_VALUE
                );
            }

            $symbols[$result['index']] = $result;
        }

        $binary = '';
        $parity = 0;

        for ($index = 0; $index < 4; $index++) {
            if (!isset($symbols[$index])) {
                throw new ReaderException(
                    "Symbol #{$index} was not given",
                    ReaderException::INVALID_SEQUENCE
                );
            } elseif ($index === 0) {
                $parity = $symbols[$index]['parity'];
            } elseif ($symbols[$index]['parity'] !== $parity) {
                throw new ReaderException(
                    "Parity mismatch in symbol #{$index}",
                    ReaderException::INVALID_SEQUENCE
                );
            }

            $binary .= substr(
                $symbols[$index]['bytes'], 5, Decoder::DATA_SIZE_QUAD / 4
            );
        }

        return $this->decoder->decodeBinary($binary);
    }

    /**
     * QRコードを読み込み、モードと生データのペアを返す
     *
     * @param string $filename
     *
     * @return array (int $mode, string $bytes)
     *
     * @throws TobidaseQR\Exception\ReaderException
     */
    private function read($filename)
    {
        $oldTrackErrors = ini_set('track_errors', 'on');
        $result = @zx_read_qrcode($filename, ZX_READ_RAW);
        ini_set('track_errors', $oldTrackErrors);
        if (!$result) {
            throw new ReaderException($php_errormsg, ReaderException::CANNOT_READ);
        }

        $bytes = $result['bytes'];
        $mode = $this->detectMode($result['version'], strlen($bytes));
        $info = $this->verifyLeadingBytes($mode, unpack('C6', $bytes));

        $result['mode'] = $mode;

        if ($mode === self::MODE_STRUCTURED_APPEND) {
            $result['index']  = $info['index'];
            $result['number'] = $info['number'];
            $result['parity'] = $info['parity'];
        }

        return $result;
    }

    /**
     * QRコードの型番からモードを判定する
     *
     * @param int $version
     * @param int $size
     *
     * @return void
     *
     * @throws TobidaseQR\Exception\ReaderException
     */
    private function detectMode($version, $size)
    {
        if ($version === self::EXPECTED_VERSION_SINGLE) {
            if ($size !== self::EXPECTED_SIZE_SINGLE) {
                throw new ReaderException(
                    "Unexpected data size {$size} for version {$version}",
                    ReaderException::UNEXPECTED_VALUE
                );
            }
            return self::MODE_8BIT;
        } elseif ($version === self::EXPECTED_VERSION_QUAD) {
            if ($size !== self::EXPECTED_SIZE_QUAD) {
                throw new ReaderException(
                    "Unexpected data size {$size} for version {$version}",
                    ReaderException::UNEXPECTED_VALUE
                );
            }
            return self::MODE_STRUCTURED_APPEND;
        } else {
            throw new ReaderException(
                "Unexpected QR Code version: {$version}",
                ReaderException::UNEXPECTED_VALUE
            );
        }
    }

    /**
     * モードとデータの先頭シーケンスが合っているか検証する
     *
     * @param int $mode
     * @param int[] $leadingBytes
     *
     * @return mixed
     *
     * @throws TobidaseQR\Exception\ReaderException
     */
    private function verifyLeadingBytes($mode, $leadingBytes)
    {
        $actualMode = ($leadingBytes[1] & 0xf0) >> 4;
        if ($mode !== $actualMode) {
            throw new ReaderException(
                "Unexpected QR Code mode {$actualMode} against expected {$mode}",
                ReaderException::UNEXPECTED_VALUE
            );
        }

        if ($mode === self::MODE_8BIT) {
            $size = (($leadingBytes[1] & 0xf) << 12)
                | ($leadingBytes[2] << 4)
                | (($leadingBytes[3] & 0xf0) >> 4);
            if ($size !== Decoder::DATA_SIZE_SINGLE) {
                throw new ReaderException(
                    "Unexpected content size {$size} for mode {$mode}",
                    ReaderException::UNEXPECTED_VALUE
                );
            }

            return true;
        } elseif ($mode === self::MODE_STRUCTURED_APPEND) {
            $index = $leadingBytes[1] & 0xf;
            if ($index > 3) {
                throw new ReaderException(
                    "Unexpected QR Code index: {$index}",
                    ReaderException::UNEXPECTED_VALUE
                );
            }

            $number = ($leadingBytes[2] & 0xf0) >> 4;
            if ($number !== 3) {
                throw new ReaderException(
                    "Unexpected total QR Code number: {$number}",
                    ReaderException::UNEXPECTED_VALUE
                );
            }

            $parity = (($leadingBytes[2] & 0xf) << 4)
                | (($leadingBytes[3] & 0xf0) >> 4);

            $contentMode = $leadingBytes[3] & 0xf;
            if ($contentMode !== self::MODE_8BIT) {
                throw new ReaderException(
                    "Unexpected content mode: {$mode}",
                    ReaderException::UNEXPECTED_VALUE
                );
            }

            $contentSize = ($leadingBytes[4] << 8) | $leadingBytes[5];
            if ($contentSize !== Decoder::DATA_SIZE_QUAD / 4) {
                throw new ReaderException(
                    "Unexpected content size {$size} for mode {$mode}",
                    ReaderException::UNEXPECTED_VALUE
                );
            }

            return [
                'index'  => $index,
                'number' => $number,
                'parity' => $parity,
            ];
        } else {
            throw new ReaderException(
                "Unsupported QR Code mode: {$mode}",
                ReaderException::UNEXPECTED_VALUE
            );
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
