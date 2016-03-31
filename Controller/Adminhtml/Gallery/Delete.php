<?php

namespace DR\Gallery\Controller\Adminhtml\Gallery;

use DR\Gallery\Controller\Adminhtml\Gallery;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\Result\Redirect;
use Exception;

class Delete extends Gallery
{
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

        $id = $this->getRequest()->getParam('gallery_id');

        if (!$id) {
            $this->messageManager->addError(__('There is no id delivered.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->galleryRepository->deleteById($id);
            $this->messageManager->addSuccess(__('You deleted the gallery.'));
            return $resultRedirect->setPath('*/*/');
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['gallery_id' => $id]);
        }
    }
}