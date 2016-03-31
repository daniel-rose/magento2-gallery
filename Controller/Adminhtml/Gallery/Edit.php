<?php

namespace DR\Gallery\Controller\Adminhtml\Gallery;

use DR\Gallery\Controller\Adminhtml\Gallery;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Exception;

class Edit extends Gallery
{
    /**
     * Dispatch request
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
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

        $data = $this->_session->getFormData(true);

        if (!empty($data)) {
            $gallery->setData($data);
        }

        $this->coreRegistry->register('dr_gallery_gallery', $gallery);

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Gallery') : __('New Gallery'),
            $id ? __('Edit Gallery') : __('New Gallery')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Galleries'));
        $resultPage->getConfig()->getTitle()->prepend($gallery->getId() ? $gallery->getName() : __('New Gallery'));

        return $resultPage;
    }
}