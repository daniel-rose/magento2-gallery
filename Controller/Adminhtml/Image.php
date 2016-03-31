<?php

namespace DR\Gallery\Controller\Adminhtml;

use DR\Gallery\Api\ImageRepositoryInterface;
use DR\Gallery\Api\Data\ImageInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Backend\Model\View\Result\Page;

abstract class Image extends Action
{
    /**
     * @var ImageInterfaceFactory
     */
    protected $imageFactory;

    /**
     * @var ImageRepositoryInterface
     */
    protected $imageRepository;

    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ImageRepositoryInterface $imageRepository
     * @param ImageInterfaceFactory $imageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ImageRepositoryInterface $imageRepository,
        ImageInterfaceFactory $imageFactory
    )
    {
        $this->coreRegistry = $coreRegistry;
        $this->imageRepository = $imageRepository;
        $this->imageFactory = $imageFactory;
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
        $resultPage->setActiveMenu('DR_Gallery::gallery_image')
            ->addBreadcrumb(__('Gallery'), __('Gallery'))
            ->addBreadcrumb(__('Images'), __('Images'));
        return $resultPage;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DR_Gallery::gallery_image');
    }
}