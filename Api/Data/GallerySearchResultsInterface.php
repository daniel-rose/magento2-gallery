<?php

namespace DR\Gallery\Api\Data;

use DR\Gallery\Api\Data\GalleryInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface GallerySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get gallery list.
     *
     * @return GalleryInterface[]
     */
    public function getItems();

    /**
     * Set galleries list.
     *
     * @param GalleryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
