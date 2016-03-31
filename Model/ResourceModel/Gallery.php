<?php

namespace DR\Gallery\Model\ResourceModel;

use DR\Gallery\Api\Data\GalleryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Gallery extends AbstractDb
{
    const TABLE_NAME = 'dr_gallery_gallery';

    /**
     * @var null|string
     */
    protected $galleryImageTable = null;

    /**
     * @var ManagerInterface|null
     */
    protected $eventManager = null;

    /**
     * Gallery constructor
     *
     * @param Context $context
     * @param null $connectionName
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        Context $context,
        ManagerInterface $eventManager,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->eventManager = $eventManager;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(static::TABLE_NAME, GalleryInterface::ID);
    }

    /**
     * Retrieve array of items id's
     *
     * array($imageId => $position)
     *
     * @param AbstractModel $object
     *
     * @return array
     */
    public function getImagesPosition(AbstractModel $object)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getGalleryImageTable(),
            ['image_id', 'position']
        )->where(
            'gallery_id = :gallery_id'
        );

        $binds = [':gallery_id' => (int) $object->getId()];

        return $connection->fetchPairs($select, $binds);
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveImages($object);
        return parent::_afterSave($object);
    }

    /**
     * Category product table name getter
     *
     * @return string
     */
    public function getGalleryImageTable()
    {
        if ($this->galleryImageTable === null) {
            $this->galleryImageTable = $this->getTable('dr_gallery_gallery_image');
        }

        return $this->galleryImageTable;
    }

    protected function saveImages(AbstractModel $gallery)
    {
        $gallery->setIsChangedImageList(false);
        $id = $gallery->getId();

        /**
         * new gallery-image relationships
         */
        $images = $gallery->getPostedImages();

        /**
         * Example re-save gallery
         */
        if ($images === null) {
            return $this;
        }

        /**
         * old gallery-image relationships
         */
        $oldImages = $gallery->getImagesPosition();

        $insert = array_diff_key($images, $oldImages);
        $delete = array_diff_key($oldImages, $images);

        /**
         * Find image ids which are presented in both arrays
         * and saved before (check $oldImages array)
         */
        $update = array_intersect_key($images, $oldImages);
        $update = array_diff_assoc($update, $oldImages);

        $connection = $this->getConnection();

        /**
         * Delete images from gallery
         */
        if (!empty($delete)) {
            $cond = ['image_id IN(?)' => array_keys($delete), 'gallery_id=?' => $id];
            $connection->delete($this->getGalleryImageTable(), $cond);
        }

        /**
         * Add images to gallery
         */
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $imageId => $position) {
                $data[] = [
                    'gallery_id' => (int) $id,
                    'image_id' => (int) $imageId,
                    'position' => (int) $position,
                ];
            }
            $connection->insertMultiple($this->getGalleryImageTable(), $data);
        }

        /**
         * Update image positions in gallery
         */
        if (!empty($update)) {
            foreach ($update as $imageId => $position) {
                $where = ['gallery_id = ?' => (int) $id, 'image_id = ?' => (int) $imageId];
                $bind = ['position' => (int) $position];
                $connection->update($this->getGalleryImageTable(), $bind, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $imageIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'dr_gallery_gallery_change_images',
                ['gallery' => $gallery, 'image_ids' => $imageIds]
            );
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $gallery->setIsChangedImageList(true);

            /**
             * Setting affected images to gallery for third party engine index refresh
             */
            $imageIds = array_keys($insert + $delete + $update);
            $gallery->setAffectedImageIds($imageIds);
        }

        return $this;
    }
}