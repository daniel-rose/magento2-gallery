<?php

namespace DR\Gallery\Block;

use DR\Gallery\Api\Data\GalleryInterface;
use DR\Gallery\Api\Data\ImageInterface;
use DR\Gallery\Api\GalleryRepositoryInterface;
use DR\Gallery\Model\ResourceModel\Image\CollectionFactory;
use Magento\Framework\View\Element\RendererList;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Gallery extends Template
{
    const DEFAULT_RENDERER_LIST_NAME = 'image.renderer.list';

    protected $gallery;

    protected $galleryRepository;

    /**
     * Gallery constructor.
     *
     * @param Context $context
     * @param GalleryRepositoryInterface $galleryRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        GalleryRepositoryInterface $galleryRepository,
        array $data = []
    )
    {
        $this->galleryRepository = $galleryRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getGallery()
    {
        if (null === $this->gallery) {
            $id = $this->getData(GalleryInterface::ID);
            $this->gallery = $this->galleryRepository->getById($id);
        }
        return $this->gallery;
    }

    /**
     * Retrieve renderer list
     *
     * @return RendererList
     */
    protected function getRendererList()
    {
        return $this->getLayout()->getBlock(static::DEFAULT_RENDERER_LIST_NAME);
    }

    /**
     * Retrieve item renderer block
     *
     * @param string|null $type
     * @return \Magento\Framework\View\Element\Template
     * @throws \RuntimeException
     */
    public function getImageRenderer($type = null)
    {
        if ($type === null) {
            $type = 'default';
        }

        $rendererList = $this->getRendererList();
        if (!$rendererList) {
            throw new \RuntimeException('Renderer list for block "' . $this->getNameInLayout() . '" is not defined');
        }

        $overriddenTemplates = $this->getOverriddenTemplates() ?: [];
        $template = isset($overriddenTemplates[$type]) ? $overriddenTemplates[$type] : $this->getRendererTemplate();
        return $rendererList->getRenderer($type, 'default', $template);
    }

    /**
     * Get image html
     *
     * @param   ImageInterface $image
     * @return  string
     */
    public function getImageHtml(ImageInterface $image)
    {
        $renderer = $this->getImageRenderer('default')->setImage($image);
        return $renderer->toHtml();
    }

    /**
     * @return null|CollectionFactory
     */
    public function getImages()
    {
        $gallery = $this->getGallery();

        if ($gallery === null || !$gallery->getId()) {
            return null;
        }

        return $gallery->getImageCollection();
    }
}