<?php
namespace TobidaseQR\Image;

use TobidaseQR\Entity\MyDesign;
use InvalidArgumentException;
use BadMethodCallException;

class Builder implements BuilderInterface
{
    const OPTION_MY_DESIGN_TYPE = 'myDesignType';

    /**
     * @var TobidaseQR\Image\Builder\AbstractBuilder
     */
    private $innerBuilder;

    /**
     * @var array
     */
    private $builderClassMap = [
        Design::TYPE_DRESS_LONG_SLEEEVED  => 'LongSleevedDress',
        Design::TYPE_DRESS_SHORT_SLEEEVED => 'NoSleeveDress',
        Design::TYPE_DRESS_NO_SLEEEVE     => 'NoSleeveDress',
        Design::TYPE_SHIRT_LONG_SLEEEVED  => 'LongSleevedShirt',
        Design::TYPE_SHIRT_SHORT_SLEEEVED => 'ShortSleevedDress',
        Design::TYPE_SHIRT_NO_SLEEEVE     => 'NoSleeveShirt',
        Design::TYPE_HAT_KNIT             => 'KnitHat',
        Design::TYPE_HAT_HORNED           => 'HornedHat',
        //Design::TYPE_UNKNOWN => '',
        Design::TYPE_GENERIC              => 'Generic',
    ];

    public function __construt(Loader $loader, array $options = [])
    {
        $type = (isset($options[self::OPTION_MY_DESIGN_TYPE]))
            ? $options[self::OPTION_MY_DESIGN_TYPE]
            : Design::TYPE_GENERIC;

        if (isset($this->builderClassMap[$type])) {
            $builderClass = 'TobidaseQR\\Image\\Builder\\'
                . $this->builderClassMap[$type] . 'Builder';
        } else {
            throw new InvalidArgumentException(
                "Argument #1 '{$type}' is not a valid type"
            );
        }

        $this->innerBuilder = new $builderClass($loader, $options);
    }

    public function getHistgram()
    {
        return $this->innerBuilder->getHistgram();
    }

    public function getPalette()
    {
        return $this->innerBuilder->getPalette();
    }

    public function getEncodedData()
    {
        return $this->innerBuilder->getEncodedData();
    }

    private function proxyGetImage($getter)
    {
        if (!method_exists($this->innerBuilder, $getter)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist',
                get_class($this->innerBuilder),
                $getter
            ));
        }

        return $this->innerBuilder->$getter();
    }

    public function getImage()
    {
        $this->innerBuilder->getImage();
    }

    public function getFrontImage()
    {
        $this->proxyGetImage('getFrontImage');
    }

    public function getBackImage()
    {
        $this->proxyGetImage('getBackImage');
    }

    public function getRightImage()
    {
        $this->proxyGetImage('getRightImage');
    }

    public function getLeftImage()
    {
        $this->proxyGetImage('getLeftImage');
    }

    private function proxySetImage($image, $setter)
    {
        if (!method_exists($this->innerBuilder, $setter)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist',
                get_class($this->innerBuilder),
                $setter
            ));
        }

        $this->innerBuilder->$setter($image);
    }

    public function setImage($image)
    {
        $this->innerBuilder->setImage($image);
    }

    public function setFrontImage($image)
    {
        $this->proxySetImage($image, 'setFrontImage');
    }

    public function setBackImage($image)
    {
        $this->proxySetImage($image, 'setBackImage');
    }

    public function setRightImage($image)
    {
        $this->proxySetImage($image, 'setRightImage');
    }

    public function setLeftImage($image)
    {
        $this->proxySetImage($image, 'setLeftImage');
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
