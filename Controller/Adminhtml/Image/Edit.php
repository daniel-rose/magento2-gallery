<?php

namespace DR\Gallery\Controller\Adminhtml\Image;

use DR\Gallery\Controller\Adminhtml\Image;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Exception;

class Edit extends Image
{
    /**
     * Dispatch request
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('image_id');

        if ($id) {
            try {
                $image = $this->imageRepository->getById($id);
            } catch(Exception $e) {
                $this->messageManager->addError(__('This image no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $image = $this->imageFactory->create();
        }

        $data = $this->_session->getFormData(true);

        if (!empty($data)) {
            $image->setData($data);
        }

        $this->coreRegistry->register('dr_gallery_image', $image);

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Image') : __('New Image'),
            $id ? __('Edit Image') : __('New Image')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Images'));
        $resultPage->getConfig()->getTitle()->prepend($image->getId() ? $image->getName() : __('New Image'));

        return $resultPage;
    }
}