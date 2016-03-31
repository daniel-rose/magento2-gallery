<?php

namespace DR\Gallery\Api\Data;

use DR\Gallery\Api\Data\ImageInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface ImageSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get images list.
     *
     * @return ImageInterface[]
     */
    public function getItems();

    /**
     * Set images list.
     *
     * @param ImageInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
