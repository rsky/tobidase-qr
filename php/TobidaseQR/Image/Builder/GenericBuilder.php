<?php
namespace TobidaseQR\Image\Builder;

use TobidaseQR\Color\Table;

class GenericBuilder extends AbstractBuilder
{
    /**
     * @var Imagick
     */
    protected $image;

    /**
     * @var array
     */
    protected $histgram;

    /**
     * @var int[]
     */
    protected $palette;

    /**
     * @var int[][]
     */
    protected $bitmap;

    /**
     * デザインタイプを返す
     *
     * @param void
     *
     * @return int
     */
    public function getType()
    {
        return \TobidaseQR\Entity\Design::TYPE_GENERIC;
    }

    public function getHistgram()
    {
        if ($this->histgram) {
            return $this->histgram;
        } elseif (!$this->image) {
            return null;
        }

        $this->histgram = $this->createHistgram($this->image);

        return $this->histgram;
    }

    public function getPalette()
    {
        if ($this->palette) {
            return $this->palette;
        } elseif (!$this->image) {
            return null;
        }

        $colors = $this->reduceColor($this->getHistgram());
        $this->palette = array_keys($colors);

        return $this->palette;
    }

    public function getBitmap()
    {
        if ($this->bitmap) {
            return $this->bitmap;
        } elseif (!$this->image) {
            return null;
        }

        $colors = $this->reduceColor($this->getHistgram());
        $table = new Table(array_values($colors));
        $this->bitmap = $this->createBitmap($this->image, $table);

        return $this->bitmap;
    }

    public function getImage()
    {
        return ($this->image) ? clone $this->image : null;
    }

    public function setImage($image)
    {
        $this->image = $this->loadImage($image, 32, 32);
        $this->histgram = null;
        $this->palette = null;
        $this->bitmap = null;
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
