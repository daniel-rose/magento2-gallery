<?php

namespace DR\Gallery\Controller\Adminhtml\Gallery;

use DR\Gallery\Controller\Adminhtml\Gallery;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Layout;

class Images extends Gallery
{
    /**
     * Dispatch request
     *
     * @return Layout
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

        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        return $resultLayout;
    }
}
