<?php
namespace TobidaseQR\Image\Builder;

trait Shirt
{
    public function setFrontImage($image)
    {
        $this->frontImage = $this->loader->load($image, 32, 32);
    }

    public function setBackImage($image)
    {
        $this->backImage = $this->loader->load($image, 32, 32);
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
