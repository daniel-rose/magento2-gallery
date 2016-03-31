<?php

namespace DR\Gallery\Controller\Adminhtml\Gallery\Widget;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutFactory;

class Chooser extends Action
{
    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }

    /**
     * Chooser Source action
     *
     * @return Raw
     */
    public function execute()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        /** @var Layout $layout */
        $layout = $this->layoutFactory->create();
        $galleriesGrid = $layout->createBlock(
            'DR\Gallery\Block\Adminhtml\Gallery\Widget\Chooser',
            '',
            ['data' => ['id' => $uniqId]]
        );
        $html = $galleriesGrid->toHtml();
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents($html);
    }
}
