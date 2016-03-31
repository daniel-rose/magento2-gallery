<?php

namespace DR\Gallery\Model;

use DR\Gallery\Api\Data\GalleryInterface;
use DR\Gallery\Model\Image\Source\Status;
use DR\Gallery\Model\ResourceModel\Gallery as GalleryResource;
use DR\Gallery\Model\ResourceModel\Image\CollectionFactory;
use DR\Gallery\Model\ResourceModel\Gallery\Collection;
use DR\Gallery\Model\ResourceModel\Image\Collection as ImageCollection;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Gallery extends AbstractModel implements GalleryInterface, IdentityInterface
{
    const CACHE_TAG = 'dr_gallery_gallery';

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * When you use true - all cache will be clean
     *
     * @var string|array|bool
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'dr_gallery_gallery';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'gallery';

    /**
     * Image collection factory
     *
     * @var CollectionFactory
     */
    protected $imageCollectionFactory;

    /**
     * Gallery constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param GalleryResource $resource
     * @param Collection $resourceCollection
     * @param array $data
     * @param CollectionFactory $imageCollectionFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        GalleryResource $resource,
        CollectionFactory $imageCollectionFactory,
        Collection $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->imageCollectionFactory = $imageCollectionFactory;
    }


    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(GalleryResource::class);
    }

    /**
     * Group Item collection
     *
     * @return ImageCollection
     */
    public function getImageCollection()
    {
        $collection = $this->imageCollectionFactory->create()
            ->setGalleryFilter($this)
            ->addFilter('status', Status::ENABLED);

        return $collection;
    }

    /**
     * Retrieve array of items id's
     *
     * array($itemId => $position)
     *
     * @return array
     */
    public function getImagesPosition()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('images_position');
        if ($array === null) {
            $array = $this->getResource()->getImagesPosition($this);
            $this->setData('images_position', $array);
        }
        return $array;
    }

    /**
     * getAvailableStatuses
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            Status::ENABLED => __('Enabled'),
            Status::DISABLED => __('Disabled')
        ];
    }

    /**
     * Retrieve id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(static::ID);
    }

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(static::NAME);
    }

    /**
     * Retrieve status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(static::STATUS);
    }

    /**
     * Retrieve created at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(static::CREATED_AT);
    }

    /**
     * Retrieve updated at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(static::UPDATED_AT);
    }

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setData(static::ID, $id);
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->setData(static::NAME, $name);
        return $this;
    }

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData(static::STATUS, $status);
        return $this;
    }

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(static::CREATED_AT, $createdAt);
        return $this;
    }

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(static::UPDATED_AT, $updatedAt);
        return $this;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}