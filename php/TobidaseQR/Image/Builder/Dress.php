<?php
namespace TobidaseQR\Image\Builder;

use TobidaseQR\Color\Table;

trait Dress
{
    protected function loadFrontImage($image)
    {
        $this->frontImage = $this->loader->loadImage($image, 32, 48);
    }

    protected function loadBackImage($image)
    {
        $this->backImage = $this->loader->loadImage($image, 32, 48);
    }

    protected function locateFrontImage(Table $table)
    {
        if (!$this->frontImage) {
            return;
        }

        $bitmap = $this->mapper->map($this->frontImage, $table);

        for ($y = 0; $y < 32; $y++) {
            for ($x = 0; $x < 32; $x++) {
                $this->segment1[$y][$x] = $bitmap[$y][$x];
            }
        }
        for ($y = 32; $y < 48; $y++) {
            for ($x = 0; $x < 32; $x++) {
                $this->segment4[$y - 32][$x] = $bitmap[$y][$x];
            }
        }
    }

    protected function locateBackImage(Table $table)
    {
        if (!$this->backImage) {
            return;
        }

        $bitmap = $this->mapper->map($this->backImage, $table);
        for ($y = 0; $y < 32; $y++) {
            for ($x = 0; $x < 32; $x++) {
                $this->segment2[$y][$x] = $bitmap[$y][$x];
            }
        }
        for ($y = 32; $y < 48; $y++) {
            for ($x = 0; $x < 32; $x++) {
                $this->segment4[$y - 16][$x] = $bitmap[$y][$x];
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
