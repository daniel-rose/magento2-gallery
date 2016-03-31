<?php

namespace DR\Gallery\Model;

use DR\Gallery\Api\Data\ImageInterface;
use DR\Gallery\Api\Data\ImageSearchResultsInterface;
use DR\Gallery\Api\Data\ImageSearchResultsInterfaceFactory;
use DR\Gallery\Api\Data\ImageInterfaceFactory;
use DR\Gallery\Api\ImageRepositoryInterface;
use DR\Gallery\Model\ResourceModel\Image as ImageResource;
use DR\Gallery\Model\ResourceModel\Image\CollectionFactory as ImageCollectionFactory;
use DR\Gallery\Model\ResourceModel\Image\Collection as ImageCollection;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Reflection\DataObjectProcessor;

class ImageRepository implements ImageRepositoryInterface
{
    /**
     * @var ImageResource
     */
    protected $resource;

    /**
     * @var ImageInterfaceFactory
     */
    protected $imageFactory;

    /**
     * @var ImageCollectionFactory
     */
    protected $imageCollectionFactory;

    /**
     * @var ImageSearchResultsInterfaceFactory
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
     * @var ImageInterface[]
     */
    protected $instances = [];

    /**
     * ImageRepository constructor
     *
     * @param ImageResource $resource
     * @param ImageInterfaceFactory $imageFactory
     * @param ImageCollectionFactory $imageCollectionFactory
     * @param ImageSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ImageResource $resource,
        ImageInterfaceFactory $imageFactory,
        ImageCollectionFactory $imageCollectionFactory,
        ImageSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    )
    {
        $this->resource = $resource;
        $this->imageFactory = $imageFactory;
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Save gallery
     *
     * @param ImageInterface $image
     * @return ImageInterface
     * @throws CouldNotSaveException
     */
    public function save(ImageInterface $image)
    {
        if (false === ($image instanceof AbstractModel)) {
            throw new CouldNotSaveException(__('Invalid Model'));
        }

        /** @var AbstractModel $image */
        try {
            $this->resource->save($image);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $image;
    }

    /**
     * Retrieve image
     *
     * @param int $imageId
     * @return ImageInterface
     * @throws NoSuchEntityException
     */
    public function getById($imageId)
    {
        if (false === array_key_exists($imageId, $this->instances)) {
            /** @var AbstractModel $image */
            $image = $this->imageFactory->create();
            $this->resource->load($image, $imageId);
            if (!$image->getId()) {
                throw new NoSuchEntityException(__('Image with id "%1" does not exist.', $imageId));
            }
            $this->instances[$imageId] = $image;
        }
        return $this->instances[$imageId];
    }

    /**
     * Retrieve images matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ImageSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var ImageSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var ImageCollection $collection */
        $collection = $this->imageCollectionFactory->create();
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
        /** @var Image $imageModel */
        foreach ($collection as $imageModel) {
            $imageData = $this->imageFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $imageData,
                $imageModel->getData(),
                ImageInterface::class
            );
            $galleries[] = $this->dataObjectProcessor->buildOutputDataArray(
                $imageData,
                ImageInterface::class
            );
        }
        $searchResults->setItems($teaserGroups);

        return $searchResults;
    }

    /**
     * Delete gallery
     *
     * @param ImageInterface $image
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(ImageInterface $image)
    {
        if (false === ($image instanceof AbstractModel)) {
            throw new CouldNotDeleteException(__('Invalid Model'));
        }
        /** @var AbstractModel $image */
        try {
            $this->resource->delete($image);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete gallery by ID.
     *
     * @param int $imageId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($imageId)
    {
        return $this->delete($this->getById($imageId));
    }
}