<?php

namespace DR\Gallery\Block\Adminhtml\Gallery\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('gallery_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Gallery Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTabAfter('images', [
            'label' => __('Images'),
            'url' => $this->getUrl('*/*/images', ['_current' => true]),
            'class' => 'ajax'
        ], 'main');

        return $this;
    }
}
