<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * バリデータクラス
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

use TobidaseQR\Exception\ValidatorException;
use TobidaseQR\Entity\Design;

/**
 * バリデータクラス
 */
class Validator
{
    /**
     * 最大長定数
     */
    const MAX_MYDESIGN_NAME_LENGTH = 12;
    const MAX_PLAYER_NAME_LENGTH = 6;
    const MAX_VILLAGE_NAME_LENGTH = 6;

    /**
     * マイデザイン名を検証する
     *
     * @param string $name
     *
     * @return void
     *
     * @throws ValidatorException
     */
    public function validateMyDesignName($name)
    {
        $this->validateString($name, self::MAX_MYDESIGN_NAME_LENGTH);
    }

    /**
     * プレイヤー名を検証する
     *
     * @param string $name
     *
     * @return void
     *
     * @throws ValidatorException
     */
    public function validatePlayerName($name)
    {
        $this->validateString($name, self::MAX_PLAYER_NAME_LENGTH);
    }

    /**
     * 村名を検証する
     *
     * @param string $name
     *
     * @return void
     *
     * @throws ValidatorException
     */
    public function validateVillageName($name)
    {
        $this->validateString($name, self::MAX_VILLAGE_NAME_LENGTH);
    }

    /**
     * 文字列を検証する
     *
     * @param string $value
     * @param int $maxLength
     *
     * @return void
     *
     * @throws ValidatorException
     */
    public function validateString($value, $maxLength)
    {
        if (mb_detect_encoding($value, 'UTF-8,ISO-8859-1', true) !== 'UTF-8') {
            throw new ValidatorException(
                'Not a valid UTF-8 string',
                ValidatorException::INVALID_ENCODING
            );
        }

        if (strlen($value) === 0) {
            throw new ValidatorException(
                'Empty value',
                ValidatorException::INVALID_LENGTH
            );
        }

        if (mb_strlen($value, 'UTF-8') > $maxLength) {
            throw new ValidatorException(
                'Too long value',
                ValidatorException::INVALID_LENGTH
            );
        }

        // @TODO 厳密なホワイトリスト方式にする
        if (preg_match('/[\\0-\\x19\\x7F]/u', $value)) {
            throw new ValidatorException(
                'Invalid characters found',
                ValidatorException::INVALID_SEQUENCE
            );
        }
    }

    /**
     * デザインタイプを検証する
     *
     * @param int $type
     * @param bool $quad
     *
     * @return void
     *
     * @throws ValidatorException
     */
    public function validateDesignType($type, $quad)
    {
        if ($quad) {
            $validTypes = [
                Design::TYPE_DRESS_LONG_SLEEEVED,
                Design::TYPE_DRESS_SHORT_SLEEEVED,
                Design::TYPE_DRESS_NO_SLEEEVE,
                Design::TYPE_SHIRT_LONG_SLEEEVED,
                Design::TYPE_SHIRT_SHORT_SLEEEVED,
                Design::TYPE_SHIRT_NO_SLEEEVE,
            ];
        } else {
            $validTypes = [
                Design::TYPE_HAT_KNIT,
                Design::TYPE_HAT_HORNED,
                Design::TYPE_GENERIC,
            ];
        }
        if (!in_array($type, $validTypes)) {
            throw new ValidatorException(
                "Invalid design type {$type}",
                ValidatorException::INVALID_VALUE
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
