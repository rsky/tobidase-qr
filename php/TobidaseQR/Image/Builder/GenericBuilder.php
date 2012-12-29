<?php
namespace TobidaseQR\Image\Builder;

class GenericBuilder extends AbstractBuilder
{
    /**
     * @var Imagick
     */
    protected $image;

    public function getHistgram()
    {
    }

    public function getPalette()
    {
    }

    public function getEncodedData()
    {
    }

    public function getImage()
    {
        return ($this->image) ? clone $this->image : null;
    }

    public function setImage($image)
    {
        $this->image = $this->loader->load($image, 32, 32);
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
