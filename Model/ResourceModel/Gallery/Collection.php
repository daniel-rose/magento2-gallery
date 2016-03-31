<?php

namespace DR\Gallery\Model\ResourceModel\Gallery;

use DR\Gallery\Api\Data\GalleryInterface;
use DR\Gallery\Model\Gallery;
use DR\Gallery\Model\ResourceModel\Gallery as GalleryResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Identifier field name for collection items
     *
     * Can be used by collections with items without defined
     *
     * @var string
     */
    protected $_idFieldName = GalleryInterface::ID;

    /**
     * Initialization here
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Gallery::class, GalleryResource::class);
    }
}