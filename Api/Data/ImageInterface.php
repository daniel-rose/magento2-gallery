<?php

namespace DR\Gallery\Api\Data;

interface ImageInterface
{
    const RELATIVE_PATH_FROM_MEDIA_TO_FILE = 'gallery' . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR;

    const ID = 'image_id';
    const NAME = 'name';
    const PATH = 'path';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Retrieve id
     *
     * @return int
     */
    public function getId();

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve path
     *
     * @return string
     */
    public function getPath();

    /**
     * Retrieve status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Retrieve created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Retrieve updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set id
     *
     * @param int $id
     * @return ImageInterface
     */
    public function setId($id);

    /**
     * Set name
     *
     * @param string $name
     * @return ImageInterface
     */
    public function setName($name);

    /**
     * Set path
     *
     * @param string $path
     * @return ImageInterface
     */
    public function setPath($path);

    /**
     * Set status
     *
     * @param int $status
     * @return ImageInterface
     */
    public function setStatus($status);

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return ImageInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return ImageInterface
     */
    public function setUpdatedAt($updatedAt);
}