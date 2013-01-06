<?php
namespace TobidaseQR\Image\Builder;

use TobidaseQR\Color\Table;
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
     * 前後左右のヒストグラム
     *
     * @var array
     */
    protected $frontHistgram;
    protected $backHistgram;
    protected $rightHistgram;
    protected $leftHistgram;

    /**
     * 前後左右のヒストグラムの合計値
     *
     * @var array
     */
    protected $histgram;

    /**
     * カラーパレット
     *
     * @var int[]
     */
    protected $palette;

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

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->segment1 = array_fill(0, 32, array_fill(0, 32, 0));
        $this->segment2 = array_fill(0, 32, array_fill(0, 32, 0));
        $this->segment3 = array_fill(0, 32, array_fill(0, 32, 0));
        $this->segment4 = array_fill(0, 32, array_fill(0, 32, 0));
    }

    public function getHistgram()
    {
        if ($this->histgram) {
            return $this->histgram;
        }

        $totalCount = 0;
        $globalHistgram = [];
        foreach (array_keys($this->table->getRgbColorTable()) as $code) {
            $globalHistgram[$code] = 0;
        }

        $histgrams = [
            $this->frontHistgram,
            $this->backHistgram,
            $this->rightHistgram,
            $this->leftHistgram,
        ];
        foreach ($histgrams as $histgram) {
            if ($histgram) {
                foreach ($histgram as $code => $count) {
                    $globalHistgram[$code] += $count;
                    $totalCount += $count;
                }
            }
        }

        if ($totalCount === 0) {
            return null;
        }

        $this->histgram = $globalHistgram;

        return $this->histgram;
    }

    public function getPalette()
    {
        if ($this->palette) {
            return $this->palette;
        }

        $histgram = $this->getHistgram();
        if (!$histgram) {
            return null;
        }

        $this->palette = array_keys($this->reduceColor($histgram));

        return $this->palette;
    }

    public function getBitmap()
    {
        $histgram = $this->getHistgram();
        if (!$histgram) {
            return null;
        }

        $colors = $this->reduceColor($this->getHistgram());
        $table = new Table(array_values($colors));
        $this->locateFrontImage($table);
        $this->locateBackImage($table);
        $this->locateRightImage($table);
        $this->locateLeftImage($table);

        return array_merge(
            $this->segment1, $this->segment2, $this->segment3, $this->segment4
        );
    }

    public function setImage($image)
    {
        $this->setFrontImage($image);
    }

    public function setFrontImage($image)
    {
        $this->loadFrontImage($image);
        $this->frontHistgram = $this->table->createHistgram(
            $this->frontImage, $this->mapper
        );
        $this->histgram = null;
        $this->palette = null;
    }

    public function setBackImage($image)
    {
        $this->loadBackImage($image);
        $this->backHistgram = $this->table->createHistgram(
            $this->backImage, $this->mapper
        );
        $this->histgram = null;
        $this->palette = null;
    }

    public function setRightImage($image)
    {
        $this->loadRightImage($image);
        $this->rightHistgram = $this->table->createHistgram(
            $this->rightImage, $this->mapper
        );
        $this->histgram = null;
        $this->palette = null;
    }

    public function setLeftImage($image)
    {
        $this->loadLeftImage($image);
        $this->leftHistgram = $this->table->createHistgram(
            $this->leftImage, $this->mapper
        );
        $this->histgram = null;
        $this->palette = null;
    }

    abstract protected function loadFrontImage($image);
    abstract protected function loadBackImage($image);
    abstract protected function loadRightImage($image);
    abstract protected function loadLeftImage($image);

    abstract protected function locateFrontImage(Table $table);
    abstract protected function locateBackImage(Table $table);
    abstract protected function locateRightImage(Table $table);
    abstract protected function locateLeftImage(Table $table);

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
