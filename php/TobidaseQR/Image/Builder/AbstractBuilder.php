<?php
namespace TobidaseQR\Image\Builder;

use TobidaseQR\Image\BuilderInterface;
use TobidaseQR\Common\ColorMapping;
use TobidaseQR\Common\ColorReduction;
use TobidaseQR\Common\ImageLoading;
use Imagick;

abstract class AbstractBuilder implements BuilderInterface
{
    use ColorMapping;
    use ColorReduction;
    use ImageLoading;

    /**
     * コンストラクタ
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setColorMappingOptions($options);
        $this->setColorReductionOptions($options);
        $this->setImageLoadingOptions($options);
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
