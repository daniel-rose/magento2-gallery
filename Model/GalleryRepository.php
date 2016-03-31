<?php

namespace DR\Gallery\Model;

use DR\Gallery\Api\Data\GalleryInterface;
use DR\Gallery\Api\Data\GallerySearchResultsInterface;
use DR\Gallery\Api\Data\GallerySearchResultsInterfaceFactory;
use DR\Gallery\Api\Data\GalleryInterfaceFactory;
use DR\Gallery\Api\GalleryRepositoryInterface;
use DR\Gallery\Model\ResourceModel\Gallery as ResourceGallery;
use DR\Gallery\Model\ResourceModel\Gallery\CollectionFactory as GalleryCollectionFactory;
use DR\Gallery\Model\ResourceModel\Gallery\Collection as GalleryCollection;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Reflection\DataObjectProcessor;

class GalleryRepository implements GalleryRepositoryInterface
{
    /**
     * @var ResourceGallery
     */
    protected $resource;

    /**
     * @var GalleryInterfaceFactory
     */
    protected $galleryFactory;

    /**
     * @var GalleryCollectionFactory
     */
    protected $galleryCollectionFactory;

    /**
     * @var GallerySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var GalleryInterface[]
     */
    protected $instances = [];

    /**
     * GalleryRepository constructor
     *
     * @param ResourceGallery $resource
     * @param GalleryInterfaceFactory $galleryFactory
     * @param GalleryCollectionFactory $galleryCollectionFactory
     * @param GallerySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ResourceGallery $resource,
        GalleryInterfaceFactory $galleryFactory,
        GalleryCollectionFactory $galleryCollectionFactory,
        GallerySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    )
    {
        $this->resource = $resource;
        $this->galleryFactory = $galleryFactory;
        $this->galleryCollectionFactory = $galleryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Save gallery
     *
     * @param GalleryInterface $gallery
     * @return GalleryInterface
     * @throws CouldNotSaveException
     */
    public function save(GalleryInterface $gallery)
    {
        if (false === ($gallery instanceof AbstractModel)) {
            throw new CouldNotSaveException(__('Invalid Model'));
        }

        /** @var AbstractModel $gallery */
        try {
            $this->resource->save($gallery);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $gallery;
    }

    /**
     * Retrieve gallery
     *
     * @param int $galleryId
     * @return GalleryInterface
     * @throws NoSuchEntityException
     */
    public function getById($galleryId)
    {
        if (false === array_key_exists($galleryId, $this->instances)) {
            /** @var AbstractModel $gallery */
            $gallery = $this->galleryFactory->create();
            $this->resource->load($gallery, $galleryId);
            if (!$gallery->getId()) {
                throw new NoSuchEntityException(__('Gallery with id "%1" does not exist.', $galleryId));
            }
            $this->instances[$galleryId] = $gallery;
        }
        return $this->instances[$galleryId];
    }

    /**
     * Retrieve galleries matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return GallerySearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var GallerySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var GalleryCollection $collection */
        $collection = $this->galleryCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $teaserGroups = [];
        /** @var Gallery $galleryModel */
        foreach ($collection as $galleryModel) {
            $galleryData = $this->galleryFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $galleryData,
                $galleryModel->getData(),
                GalleryInterface::class
            );
            $galleries[] = $this->dataObjectProcessor->buildOutputDataArray(
                $galleryData,
                GalleryInterface::class
            );
        }
        $searchResults->setItems($teaserGroups);

        return $searchResults;
    }

    /**
     * Delete gallery
     *
     * @param GalleryInterface $gallery
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(GalleryInterface $gallery)
    {
        if (false === ($gallery instanceof AbstractModel)) {
            throw new CouldNotDeleteException(__('Invalid Model'));
        }
        /** @var AbstractModel $gallery */
        try {
            $this->resource->delete($gallery);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete gallery by ID.
     *
     * @param int $galleryId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($galleryId)
    {
        return $this->delete($this->getById($galleryId));
    }
}