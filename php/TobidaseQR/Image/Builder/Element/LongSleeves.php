<?php
namespace TobidaseQR\Image\Builder\Element;

use TobidaseQR\Color\Table;

trait LongSleeves
{
    protected function loadLeftImage($image)
    {
        $this->leftImage = $this->loadImage($image, 16, 32);
    }

    protected function loadRightImage($image)
    {
        $this->rightImage = $this->loadImage($image, 16, 32);
    }

    protected function locateLeftImage(Table $table)
    {
        if (!$this->leftImage) {
            return;
        }

        $bitmap = $this->createBitmap($this->leftImage, $table);
        for ($y = 0; $y < 32; $y++) {
            for ($x = 0; $x < 16; $x++) {
                $this->segment3[31 - $x][$y] = $bitmap[$y][$x];
            }
        }
    }

    protected function locateRightImage(Table $table)
    {
        if (!$this->rightImage) {
            return;
        }

        $bitmap = $this->createBitmap($this->rightImage, $table);
        for ($y = 0; $y < 32; $y++) {
            for ($x = 0; $x < 16; $x++) {
                $this->segment3[15 - $x][$y] = $bitmap[$y][$x];
            }
        }
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
