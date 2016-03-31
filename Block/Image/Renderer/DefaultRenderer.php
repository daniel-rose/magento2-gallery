<?php

namespace DR\Gallery\Block\Image\Renderer;

use DR\Gallery\Api\Data\ImageInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

class DefaultRenderer extends Template implements IdentityInterface
{
    protected $image;

    /**
     * Set image for render
     *
     * @param ImageInterface $image
     * @return $this
     * @codeCoverageIgnore
     */
    public function setImage(ImageInterface $image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return ImageInterface
     * @codeCoverageIgnore
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        if ($this->getImage()) {
            $identities = $this->getImage()->getIdentities();
        }
        return $identities;
    }
}