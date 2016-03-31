<?php

namespace DR\Gallery\Api;

use DR\Gallery\Api\Data\GalleryInterface;
use DR\Gallery\Api\Data\GallerySearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface GalleryRepositoryInterface
{
    /**
     * Save gallery
     *
     * @param GalleryInterface $gallery
     * @return GalleryInterface
     * @throws CouldNotSaveException
     */
    public function save(GalleryInterface $gallery);

    /**
     * Retrieve gallery
     *
     * @param int $galleryId
     * @return GalleryInterface
     * @throws NoSuchEntityException
     */
    public function getById($galleryId);

    /**
     * Retrieve galleries matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return GallerySearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete image
     *
     * @param GalleryInterface $gallery
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(GalleryInterface $gallery);

    /**
     * Delete gallery by ID.
     *
     * @param int $galleryId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($galleryId);
}
