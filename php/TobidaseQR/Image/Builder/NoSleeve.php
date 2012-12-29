<?php
namespace TobidaseQR\Image\Builder;

use DomainException;

trait NoSleeve
{
    public function setLeftImage($image)
    {
        throw new DomainException('There is no sleeve');
    }

    public function setRightImage($image)
    {
        throw new DomainException('There is no sleeve');
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
