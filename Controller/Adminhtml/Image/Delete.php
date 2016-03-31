<?php

namespace DR\Gallery\Controller\Adminhtml\Image;

use DR\Gallery\Controller\Adminhtml\Image;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\Result\Redirect;
use Exception;

class Delete extends Image
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

        $id = $this->getRequest()->getParam('image_id');

        if (!$id) {
            $this->messageManager->addError(__('There is no id delivered.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->imageRepository->deleteById($id);
            $this->messageManager->addSuccess(__('You deleted the image.'));
            return $resultRedirect->setPath('*/*/');
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['image_id' => $id]);
        }
    }
}