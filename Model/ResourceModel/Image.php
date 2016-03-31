<?php

namespace DR\Gallery\Model\ResourceModel;

use DR\Gallery\Api\Data\ImageInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Image extends AbstractDb
{
    const TABLE_NAME = 'dr_gallery_image';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(static::TABLE_NAME, ImageInterface::ID);
    }
}