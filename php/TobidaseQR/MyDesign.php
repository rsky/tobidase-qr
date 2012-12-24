<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * マイデザインエンティティクラス
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

use TobidaseQR\Image\Loader;
use Imagick;
use RangeException;
use UnexpectedValueException;

/**
 * マイデザインエンティティクラス
 */
class MyDesign
{
    /**
     * 画像タイプ定数
     */
    const TYPE_DEFAULT = 0;

    /**
     * マイデザイン名
     *
     * @var string
     */
    private $name;

    /**
     * 画像タイプ
     *
     * @var int
     */
    private $type;

    /**
     * 幅
     *
     * @var int
     */
    private $width;

    /**
     * 高さ
     *
     * @var int
     */
    private $height;

    /**
     * カラーテーブル
     *
     * @var array
     */
    private $table;

    /**
     * 画像データ
     *
     * @var int[][]
     */
    private $rows;

    /**
     * コンストラクタ
     *
     * @param int $type
     */
    public function __construct($type = self::TYPE_DEFAULT)
    {
        list($width, $height) = $this->getSizeForType($type);
        $this->type = $type;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * 画像タイプに応じたサイズを返す
     *
     * @param int $type
     *
     * @return int[] ($width, $height)
     */
    public function getSizeForType($type)
    {
        switch ($type) {
            case self::TYPE_DEFAULT:
                return [32, 32];
        }

        throw new UnexpectedValueException("Unsupported type: {$type}");
    }

    /**
     * マイデザイン名を取得する
     *
     * @param void
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * カラーテーブルを取得する
     *
     * @param void
     *
     * @return array
     */
    public function getColorTable()
    {
        return $this->table;
    }

    /**
     * カラーパレットを取得する
     *
     * @param void
     *
     * @return int[]
     */
    public function getPalette()
    {
        return array_keys($this->table);
    }

    /**
     * ピクセルデータ（カラーコードの2次元配列）を取得する
     *
     * @param void
     *
     * @return int[][]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * マイデザイン名をセットする
     *
     * @param string $name
     *
     * @return void
     *
     * @throws UnexpectedValueException, RangeException
     */
    public function setName($name)
    {
        if (mb_detect_encoding($name, 'UTF-8,ISO-8859-1', true) !== 'UTF-8') {
            throw new UnexpectedValueException('Not a valid UTF-8 string');
        }

        $name = trim($name);
        if (preg_match('/[\\0-\\x19\\x7F]/u', $name)) {
            throw new UnexpectedValueException('Contains invalid characters');
        }
        if ($name === '' || mb_strlen($name, 'UTF-8') > 12) {
            throw new RangeException('Must be 1-12 characters');
        }

        $this->name = $name;
    }

    /**
     * 画像を読み込む
     *
     * @param Imagick $image
     * @param array $options
     *
     * @return void
     *
     * @see TobidaseQR\ImageLoader::load()
     */
    public function loadImage(Imagick $image, array $options = [])
    {
        $loader = new Loader($this->width, $this->height);
        list($table, $data) = $loader->load($image, $options);
        $this->table = $table;
        $this->data = $data;
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
