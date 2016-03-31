<?php

namespace DR\Gallery\Block\Adminhtml\Gallery\Widget;

use DR\Gallery\Api\GalleryRepositoryInterface;
use DR\Gallery\Model\ResourceModel\Gallery\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;

class Chooser extends Extended
{
    protected $collectionFactory;

    protected $galleryRepository;

    public function __construct(
        Context $context,
        Data $backendHelper,
        GalleryRepositoryInterface $galleryRepository,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->galleryRepository = $galleryRepository;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Block construction, prepare grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('gallery/gallery_widget/chooser', ['uniq_id' => $uniqId]);

        $chooser = $this->getLayout()->createBlock(
            'Magento\Widget\Block\Adminhtml\Widget\Chooser'
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        if ($element->getValue()) {
            try {
                $gallery = $this->galleryRepository->getById((int) $element->getValue());
                $chooser->setLabel($this->escapeHtml($gallery->getName()));
            } catch (\Exception $e) { }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();

        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var galleryName = trElement.down("td").next().innerHTML;
                var galleryId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                ' .
            $chooserJsObject .
            '.setElementValue(galleryId);
                ' .
            $chooserJsObject .
            '.setElementLabel(galleryName);
                ' .
            $chooserJsObject .
            '.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare pages collection
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for pages grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'chooser_id',
            [
                'header' => __('ID'),
                'index' => 'gallery_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'chooser_title',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-title',
                'column_css_class' => 'col-title'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('gallery/gallery_widget/chooser', ['_current' => true]);
    }
}