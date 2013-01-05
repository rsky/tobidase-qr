<?php
namespace TobidaseQR\Image\Builder;

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

    public function getHistgram()
    {
        if ($this->histgram) {
            return $this->histgram;
        } elseif (!$this->image) {
            return null;
        }

        $this->histgram = $this->table->createHistgram(
            $this->image, $this->mapper
        );

        return $this->histgram;
    }

    public function getPalette()
    {
        if ($this->palette) {
            return $this->palette;
        } elseif (!$this->image) {
            return null;
        }

        $this->palette = array_keys(
            $this->reduceColor($this->getHistgram())->getRgbColorTable()
        );

        return $this->palette;
    }

    public function getBitmap()
    {
        if ($this->bitmap) {
            return $this->bitmap;
        } elseif (!$this->image) {
            return null;
        }

        $this->bitmap = $this->mapper->map(
            $this->image, $this->reduceColor($this->getHistgram())
        );

        return $this->bitmap;
    }

    public function getImage()
    {
        return ($this->image) ? clone $this->image : null;
    }

    public function setImage($image)
    {
        $this->image = $this->loader->load($image, 32, 32);
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
