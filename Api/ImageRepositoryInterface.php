<?php

namespace DR\Gallery\Api;

use DR\Gallery\Api\Data\ImageInterface;
use DR\Gallery\Api\Data\ImageSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface ImageRepositoryInterface
{
    /**
     * Save image
     *
     * @param ImageInterface $image
     * @return ImageInterface
     * @throws CouldNotSaveException
     */
    public function save(ImageInterface $image);

    /**
     * Retrieve image
     *
     * @param int $imageId
     * @return ImageInterface
     * @throws LocalizedException
     */
    public function getById($imageId);

    /**
     * Retrieve images matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ImageSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete image
     *
     * @param ImageInterface $image
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(ImageInterface $image);

    /**
     * Delete image by ID.
     *
     * @param int $imageId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($imageId);
}
