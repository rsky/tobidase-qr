<?php
namespace TobidaseQR\Image\Builder;

class NoSleeveShiftBuilder extends CombinedBuilder
{
    use Element\Shirt;
    use Element\NoSleeve;

    /**
     * デザインタイプを返す
     *
     * @param void
     *
     * @return int
     */
    public function getType()
    {
        return \TobidaseQR\Entity\Design::TYPE_SHIRT_NO_SLEEEVE;
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
