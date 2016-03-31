<?php

namespace DR\Gallery\Model\ResourceModel\Image;

use DR\Gallery\Api\Data\ImageInterface;
use DR\Gallery\Model\Gallery;
use DR\Gallery\Model\Image;
use DR\Gallery\Model\ResourceModel\Image as ImageResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\DB\Select;

class Collection extends AbstractCollection
{
    /**
     * Identifier field name for collection items
     *
     * Can be used by collections with items without defined
     *
     * @var string
     */
    protected $_idFieldName = ImageInterface::ID;

    /**
     * Initialization here
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Image::class, ImageResource::class);
    }

    /**
     * @param $gallery
     * @param bool $sortByPosition
     * @return $this
     */
    public function setGalleryFilter($gallery, $sortByPosition = true)
    {
        if ($gallery === null || !($gallery instanceof Gallery) || $gallery->getId() === null) {
            return $this;
        }

        $this->getSelect()->joinInner(
            ['gi' => $this->getTable('dr_gallery_gallery_image')],
            'main_table.image_id = gi.image_id',
            ['gallery_id' => 'gi.gallery_id', 'position' => 'position']
        )->where('gi.gallery_id = ?', $gallery->getId());

        if ($sortByPosition) {
            $this->getSelect()->order('gi.position ' . Select::SQL_ASC);
        }

        return $this;
    }
}