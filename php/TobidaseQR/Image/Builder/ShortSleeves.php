<?php
namespace TobidaseQR\Image\Builder;

trait ShortSleeves
{
    public function setLeftImage($image)
    {
        $this->leftImage = $this->loader->load($image, 16, 16);
    }

    public function setRightImage($image)
    {
        $this->rightImage = $this->loader->load($image, 16, 16);
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
