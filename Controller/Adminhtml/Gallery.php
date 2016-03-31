<?php

namespace DR\Gallery\Controller\Adminhtml;

use DR\Gallery\Api\GalleryRepositoryInterface;
use DR\Gallery\Api\Data\GalleryInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Backend\Model\View\Result\Page;

abstract class Gallery extends Action
{
    /**
     * @var GalleryInterfaceFactory
     */
    protected $galleryFactory;

    /**
     * @var GalleryRepositoryInterface
     */
    protected $galleryRepository;

    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param GalleryRepositoryInterface $galleryRepository
     * @param GalleryInterfaceFactory $galleryFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        GalleryRepositoryInterface $galleryRepository,
        GalleryInterfaceFactory $galleryFactory
    )
    {
        $this->coreRegistry = $coreRegistry;
        $this->galleryRepository = $galleryRepository;
        $this->galleryFactory = $galleryFactory;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    protected function initPage(Page $resultPage)
    {
        $resultPage->setActiveMenu('DR_Gallery::gallery_gallery')
            ->addBreadcrumb(__('Gallery'), __('Gallery'))
            ->addBreadcrumb(__('Galleries'), __('Galleries'));
        return $resultPage;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DR_Gallery::gallery_gallery');
    }
}