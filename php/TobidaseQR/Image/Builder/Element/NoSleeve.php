<?php
namespace TobidaseQR\Image\Builder\Element;

use DomainException;

trait NoSleeve
{
    protected function loadLeftImage($image)
    {
        throw new DomainException('There is no sleeve');
    }

    protected function loadRightImage($image)
    {
        throw new DomainException('There is no sleeve');
    }

    protected function locateRightImage(Table $table)
    {
        // pass
    }

    protected function locateLeftImage(Table $table)
    {
        // pass
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
