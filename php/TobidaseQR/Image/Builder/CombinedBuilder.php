<?php
namespace TobidaseQR\Image\Builder;

use TobidaseQR\Image\Loader;

abstract class CombinedBuilder extends AbstractBuilder
{
    /**
     * 前後左右の画像データ
     *
     * @var Imagick
     */
    protected $frontImage;
    protected $backImage;
    protected $rightImage;
    protected $leftImage;

    /**
     * 前面のデータを格納する領域
     *
     * @var array
     */
    protected $segment1;

    /**
     * 背面のデータを格納する領域
     *
     * @var array
     */
    protected $segment2;

    /**
     * 袖のデータを格納する領域
     *
     * @var array
     */
    protected $segment3;

    /**
     * 前面下部と背面下部のデータを格納する領域
     *
     * @var array
     */
    protected $segment4;

    public function __construct(Loader $loader, array $options = [])
    {
        parent::__construct($loader, $options);

        $this->segment1 = array_fill(0, 32, array_fill(0, 32, 0));
        $this->segment2 = array_fill(0, 32, array_fill(0, 32, 0));
        $this->segment3 = array_fill(0, 32, array_fill(0, 32, 0));
        $this->segment4 = array_fill(0, 32, array_fill(0, 32, 0));
    }

    public function getHistgram()
    {
    }

    public function getPalette()
    {
    }

    public function getEncodedData()
    {
    }

    public function setImage($image)
    {
        $this->setFrontImage($image);
    }

    abstract public function setFrontImage($image);
    abstract public function setBackImage($image);
    abstract public function setRightImage($image);
    abstract public function setLeftImage($image);

    abstract protected function encodeFrontImage();
    abstract protected function encodeBackImage();
    abstract protected function encodeRightImage();
    abstract protected function encodeLeftImage();

    public function getImage()
    {
        return $this->getFrontImage();
    }

    public function getFrontImage()
    {
        return ($this->frontImage) ? clone $this->frontImage : null;
    }

    public function getBackImage()
    {
        return ($this->backImage) ? clone $this->backImage : null;
    }

    public function getRightImage()
    {
        return ($this->rightImage) ? clone $this->rightImage : null;
    }

    public function getLeftImage()
    {
        return ($this->leftImage) ? clone $this->leftImage : null;
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
