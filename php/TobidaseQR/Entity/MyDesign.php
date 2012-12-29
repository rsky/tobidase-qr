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

use TobidaseQR\JSONSerializable;

/**
 * マイデザインエンティティクラス
 */
class MyDesign implements JSONSerializable
{
    /**
     * マイデザイン名
     *
     * @var string
     */
    public $name;

    /**
     * プレイヤー
     *
     * @var TobidaseQR\Entity\Player
     */
    public $player;

    /**
     * 村
     *
     * @var TobidaseQR\Entity\Village
     */
    public $village;

    /**
     * デザイン
     *
     * @var TobidaseQR\Entity\Design
     */
    public $design;

    /**
     * 拡張属性
     *
     * @var TobidaseQR\Entity\HeaderExtra
     */
    public $headerExtra;

    /**
     * JSON表現を返す
     *
     * @param int $options
     *
     * @return string
     */
    public function exportJson($options = 0)
    {
        $childOptions = $options & ~JSON_PRETTY_PRINT;

        return json_encode([
            'name' => $this->name,
            'player' => (is_object($this->player))
                ? $this->player->exportJson($childOptions)
                : null,
            'village' => (is_object($this->village))
                ? $this->village->exportJson($childOptions)
                : null,
            'design' => (is_object($this->design))
                ? $this->design->exportJson($childOptions)
                : null,
            'headerExtra' => (is_object($this->headerExtra))
                ? $this->headerExtra->exportJson($childOptions)
                : null,
        ], $options);
    }

    /**
     * JSONから値を復元する
     *
     * @param string $json
     *
     * @return void
     */
    public function importJson($json)
    {
        $attributes = json_decode($json, true);
        if (!$attributes) {
            return;
        }

        foreach ($attributes as $attr => $value) {
            if (!property_exists($this, $attr)) {
                continue;
            }

            if (is_null($value)) {
                $this->$attr = null;
                continue;
            }

            $entityClass = 'TobidaseQR\\Entity\\' . ucfirst($attr);
            if (class_exists($entityClass)) {
                if (!$this->$attr) {
                    $this->$attr = new $entityClass;
                }
                $this->$attr->importJson($value);
            } else {
                $this->$attr = $value;
            }
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
