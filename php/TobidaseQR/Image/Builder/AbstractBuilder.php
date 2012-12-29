<?php
namespace TobidaseQR\Image\Builder;

use TobidaseQR\Image\BuilderInterface;
use TobidaseQR\Image\Loader;
use Imagick;

abstract class AbstractBuilder implements BuilderInterface
{
    const OPTION_RESIZE_FILTER = 'resizeFilter';
    const OPTION_RESIZE_BLUR   = 'resizeBlur';

    /**
     * @var TobidaseQR\Image\Loader
     */
    protected $loader;

    /**
     * @var int
     */
    private $filter;

    /**
     * @var float
     */
    private $blur;

    public function __construct(Loader $loader, array $options = [])
    {
        $this->loader = $loader;

        $this->filter = (isset($options[self::OPTION_RESIZE_FILTER]))
            ? (int)$options[self::OPTION_RESIZE_FILTER]
            : Imagick::FILTER_LANCZOS;

        $this->blur = (isset($options[self::OPTION_RESIZE_BLUR]))
            ? (float)$options[self::OPTION_RESIZE_BLUR]
            : 1.0;
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
