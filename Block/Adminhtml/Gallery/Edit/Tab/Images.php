<?php

namespace DR\Gallery\Block\Adminhtml\Gallery\Edit\Tab;

use DR\Gallery\Api\Data\GalleryInterface;
use DR\Gallery\Api\Data\ImageInterface;
use DR\Gallery\Model\ResourceModel\Image\CollectionFactory;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;

class Images extends Extended
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var  CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        Registry $coreRegistry,
        array $data = []
    )
    {
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gallery_image_grid');
        $this->setDefaultSort('image_id', 'desc');
        $this->setUseAjax(true);

        if ($this->getGallery() && $this->getGallery()->getId() && 0 < count($this->getSelectedImages())) {
            $this->setDefaultFilter(['in_gallery' => 1]);
        }
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $collection->getSelect()->joinLeft(
            ['link_table' => $collection->getTable('dr_gallery_gallery_image')],
            'main_table.image_id = link_table.image_id',
            ['gallery_id', 'position']
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_gallery', [
            'type' => 'checkbox',
            'name' => 'in_gallery',
            'values' => $this->getSelectedImages(),
            'index' => 'image_id',
            'filter_index' => 'link_table.image_id',
            'header_css_class' => 'col-select col-massaction',
            'column_css_class' => 'col-select col-massaction'
        ]);

        $this->addColumn(ImageInterface::ID, [
            'header' => __('Image Id'),
            'index' => 'image_id',
            'filter_index' => 'link_table.image_id',
        ]);

        $this->addColumn('image_name', [
            'header' => __('Name'),
            'index' => ImageInterface::NAME
        ]);

        $this->addColumn('image_created_at', [
            'header' => __('Created At'),
            'index' => ImageInterface::CREATED_AT
        ]);

        $this->addColumn('image_updated_at', [
            'header' => __('Updated Time'),
            'index' => ImageInterface::UPDATED_AT
        ]);

        $this->addColumn('position', [
            'header' => __('Position'),
            'type' => 'number',
            'index' => 'position',
            'editable' => true
        ]);

        $this->addColumn('position', [
                'header' => __('Position'),
                'name' => 'position',
                'type' => 'number',
                'validate_class' => 'validate-number',
                'index' => 'position',
                'editable' => true,
                'edit_only' => !$this->getGallery()->getId(),
                'header_css_class' => 'col-position',
                'column_css_class' => 'col-position',
                'filter_condition_callback' => [$this, 'filterImagesPosition']
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Add filter
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_gallery') {
            $imageIds = $this->getSelectedImages();
            if (empty($imageIds)) {
                $imageIds = 0;
            }
            if ($column->getFilter()->getData('value')) {
                $this->getCollection()->addFieldToFilter('main_table.image_id', ['in' => $imageIds]);
            } else {
                if ($imageIds) {
                    $this->getCollection()->addFieldToFilter('main_table.image_id', ['nin' => $imageIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Retrieve selected images
     *
     * @return array
     */
    protected function getSelectedImages()
    {
        return array_keys($this->getSelectedImagePositions());
    }

    /**
     * @return array
     */
    public function getSelectedImagePositions()
    {
        if (false === $this->hasData('selected_image_positions')) {
            $images = [];
            foreach ($this->getGallery()->getImagesPosition() as $imageId => $imagePosition) {
                $images[$imageId] = ['position' => $imagePosition];
            }

            $this->setData('selected_image_positions', $images);
        }


        return $this->getData('selected_image_positions');
    }

    /**
     * @return GalleryInterface
     */
    public function getGallery()
    {
        return $this->coreRegistry->registry('dr_gallery_gallery');
    }

    /**
     * Apply `position` filter to images grid.
     *
     * @param Collection $collection
     * @param Extended $column
     * @return $this
     */
    public function filterImagesPosition($collection, $column)
    {
        $collection->addFieldToFilter($column->getIndex(), $column->getFilter()->getCondition());
        return $this;
    }
}