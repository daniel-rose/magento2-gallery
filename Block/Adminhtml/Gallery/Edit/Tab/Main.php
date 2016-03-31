<?php

namespace DR\Gallery\Block\Adminhtml\Gallery\Edit\Tab;

use DR\Gallery\Api\Data\GalleryInterface;
use DR\Gallery\Model\Gallery;
use DR\Gallery\Model\Gallery\Source\Status;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;

class Main extends Generic implements TabInterface
{
    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->setData('active', true);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model Gallery */
        $model = $this->_coreRegistry->registry('dr_gallery_gallery');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('gallery_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Gallery Information')]);

        if ($model->getId()) {
            $fieldset->addField(GalleryInterface::ID, 'hidden', ['name' => GalleryInterface::ID]);
        }

        $fieldset->addField(
            GalleryInterface::NAME,
            'text',
            [
                'name' => GalleryInterface::NAME,
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            GalleryInterface::STATUS,
            'select',
            [
                'label' => __('Status'),
                'title' => __('Page Status'),
                'name' => GalleryInterface::STATUS,
                'required' => true,
                'options' => $model->getAvailableStatuses()
            ]
        );
        if (!$model->getId()) {
            $model->setData(GalleryInterface::STATUS, Status::ENABLED);
        }

        $this->_eventManager->dispatch('adminhtml_dr_gallery_gallery_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
