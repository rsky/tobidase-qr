<?php
namespace TobidaseQR\Image;

interface BuilderInterface
{
    public function __construt(Loader $loader, array $options = []);

    public function getHistgram();

    public function getPalette();

    public function getEncodedData();

    public function getImage($image);

    public function setImage($image);
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
