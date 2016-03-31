<?php

namespace DR\Gallery\Controller\Adminhtml\Gallery;

use DR\Gallery\Controller\Adminhtml\Gallery;
use DR\Gallery\Api\GalleryRepositoryInterface;
use DR\Gallery\Api\Data\GalleryInterfaceFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Model\AbstractModel;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Backend\Helper\Js;
use Exception;

class Save extends Gallery
{
    /**
     * @var Js
     */
    protected $jsHelper;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param GalleryRepositoryInterface $galleryRepository
     * @param GalleryInterfaceFactory $galleryFactory
     * @param Js $jsHelper
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        GalleryRepositoryInterface $galleryRepository,
        GalleryInterfaceFactory $galleryFactory,
        Js $jsHelper
    )
    {
        parent::__construct($context, $coreRegistry, $galleryRepository, $galleryFactory);
        $this->jsHelper = $jsHelper;
    }

    /**
     * Dispatch request
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = $this->getRequest()->getParam('gallery_id');

            if ($id) {
                try {
                    $gallery = $this->galleryRepository->getById($id);
                } catch(Exception $e) {
                    $this->messageManager->addError(__('This gallery no longer exists.'));
                    /** @var Redirect $resultRedirect */
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                $gallery = $this->galleryFactory->create();
            }

            $gallery->setData($data);
            $gallery = $this->decodeImageLinks($gallery);

            try {
                $this->galleryRepository->save($gallery);
                $this->messageManager->addSuccess(__('You saved the gallery.'));
                $this->_session->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['gallery_id' => $gallery->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setFormData($data);
                return $resultRedirect->setPath('*/*/edit', ['gallery_id' => $id]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractModel
     */
    public function decodeImageLinks(AbstractModel $object)
    {
        if (false === $object->hasData('links')
            || false === array_key_exists('images', $object->getData('links'))
            || !$object->getData('links')['images']
        ) {
            return $object;
        }
        
        $postedImages = $this->jsHelper->decodeGridSerializedInput($object->getData('links')['images']);
       
        array_walk($postedImages, function (&$item) {
            $item = $item['position'];
        });
        return $object->setData('posted_images', $postedImages);
    }
}