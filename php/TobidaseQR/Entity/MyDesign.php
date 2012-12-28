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

namespace TobidaseQR\Entity;

use TobidaseQR\Validator;
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
     * デザインタイプ定数
     */
    // ワンピース（長袖、半袖、ノースリーブ）
    const TYPE_DRESS_LONG_SLEEEVED  = 0;
    const TYPE_DRESS_SHORT_SLEEEVED = 1;
    const TYPE_DRESS_NO_SLEEEVE     = 2;
    // Tシャツ（長袖、半袖、ノースリーブ）
    const TYPE_SHIRT_LONG_SLEEEVED  = 3;
    const TYPE_SHIRT_SHORT_SLEEEVED = 4;
    const TYPE_SHIRT_NO_SLEEEVE     = 5;
    // 帽子（ニット帽、つの帽子）
    const TYPE_HAT_KNIT   = 6;
    const TYPE_HAT_HORNED = 7;
    // 不明
    const TYPE_UNKNOWN = 8;
    // 一般
    const TYPE_GENERIC = 9;

    /**
     * マイデザイン名
     *
     * @var string
     */
    private $name;

    /**
     * デザインタイプ
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
    public function __construct($type = self::TYPE_GENERIC)
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
            case self::TYPE_GENERIC:
            case self::TYPE_HAT_KNIT:
            case self::TYPE_HAT_HORNED:
                return [32, 32];
            case self::TYPE_DRESS_LONG_SLEEEVED:
            case self::TYPE_DRESS_SHORT_SLEEEVED:
            case self::TYPE_DRESS_NO_SLEEEVE:
            case self::TYPE_SHIRT_LONG_SLEEEVED:
            case self::TYPE_SHIRT_SHORT_SLEEEVED:
            case self::TYPE_SHIRT_NO_SLEEEVE:
                return [64, 64];
        }

        throw new UnexpectedValueException("Unsupported type: {$type}");
    }

    /**
     * デザインタイプを返す
     *
     * @param void
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * マイデザイン名を返す
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
     * カラーテーブルを返す
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
     * カラーパレットを返す
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
     * ピクセルデータ（カラーコードの2次元配列）を返す
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
        (new Validator)->validateMyDesignName($name);
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
