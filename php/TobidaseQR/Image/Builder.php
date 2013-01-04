<?php
/**
 * PHP version 5.4
 *
 * とびだせ どうぶつの森™ マイデザインQRコードジェネレータ
 * 任意の画像をQRコード用に変換するクラス
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

namespace TobidaseQR\Image;

use TobidaseQR\Color\Mapper;
use TobidaseQR\Common\Option;
use InvalidArgumentException;
use BadMethodCallException;

/**
 * 任意の画像をQRコード用に変換するクラス
 */
class Builder implements BuilderInterface
{
    /**
     * 実際の変換を行うオブジェクト
     *
     * @var TobidaseQR\Image\Builder\AbstractBuilder
     */
    private $innerBuilder;

    /**
     * @var array
     */
    private $builderClassMap = [
        Design::TYPE_DRESS_LONG_SLEEEVED  => 'LongSleevedDress',
        Design::TYPE_DRESS_SHORT_SLEEEVED => 'NoSleeveDress',
        Design::TYPE_DRESS_NO_SLEEEVE     => 'NoSleeveDress',
        Design::TYPE_SHIRT_LONG_SLEEEVED  => 'LongSleevedShirt',
        Design::TYPE_SHIRT_SHORT_SLEEEVED => 'ShortSleevedDress',
        Design::TYPE_SHIRT_NO_SLEEEVE     => 'NoSleeveShirt',
        Design::TYPE_HAT_KNIT             => 'KnitHat',
        Design::TYPE_HAT_HORNED           => 'HornedHat',
        //Design::TYPE_UNKNOWN => '',
        Design::TYPE_GENERIC              => 'Generic',
    ];

    /**
     * コンストラクタ
     *
     * @param array $options
     */
    public function __construt(array $options = [])
    {
        $type = (isset($options[Option::MY_DESIGN_TYPE]))
            ? $options[Option::MY_DESIGN_TYPE]
            : Design::TYPE_GENERIC;

        if (isset($this->builderClassMap[$type])) {
            $builderClass = 'TobidaseQR\\Image\\Builder\\'
                . $this->builderClassMap[$type] . 'Builder';
        } else {
            throw new InvalidArgumentException(
                "Argument #1 '{$type}' is not a valid type"
            );
        }

        $this->innerBuilder = new $builderClass($options);
    }

    public function getHistgram()
    {
        return $this->innerBuilder->getHistgram();
    }

    public function getPalette()
    {
        return $this->innerBuilder->getPalette();
    }

    public function getBitmap()
    {
        return $this->innerBuilder->getBitmap();
    }

    private function proxyGetImage($getter)
    {
        if (!method_exists($this->innerBuilder, $getter)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist',
                get_class($this->innerBuilder),
                $getter
            ));
        }

        return $this->innerBuilder->$getter();
    }

    public function getImage()
    {
        $this->innerBuilder->getImage();
    }

    public function getFrontImage()
    {
        $this->proxyGetImage('getFrontImage');
    }

    public function getBackImage()
    {
        $this->proxyGetImage('getBackImage');
    }

    public function getRightImage()
    {
        $this->proxyGetImage('getRightImage');
    }

    public function getLeftImage()
    {
        $this->proxyGetImage('getLeftImage');
    }

    private function proxySetImage($image, $setter)
    {
        if (!method_exists($this->innerBuilder, $setter)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist',
                get_class($this->innerBuilder),
                $setter
            ));
        }

        $this->innerBuilder->$setter($image);
    }

    public function setImage($image)
    {
        $this->innerBuilder->setImage($image);
    }

    public function setFrontImage($image)
    {
        $this->proxySetImage($image, 'setFrontImage');
    }

    public function setBackImage($image)
    {
        $this->proxySetImage($image, 'setBackImage');
    }

    public function setRightImage($image)
    {
        $this->proxySetImage($image, 'setRightImage');
    }

    public function setLeftImage($image)
    {
        $this->proxySetImage($image, 'setLeftImage');
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
