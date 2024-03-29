<?php
namespace TobidaseQR\Image\Builder;

class LongSleevedShirtBuilder extends CombinedBuilder
{
    use Element\Shirt;
    use Element\LongSleeves;

    /**
     * デザインタイプを返す
     *
     * @param void
     *
     * @return int
     */
    public function getType()
    {
        return \TobidaseQR\Entity\Design::SHIRT_LONG_SLEEEVED;
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
