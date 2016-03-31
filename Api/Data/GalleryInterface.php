<?php

namespace DR\Gallery\Api\Data;

interface GalleryInterface
{
    const ID = 'gallery_id';
    const NAME = 'name';
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
     * @return $this
     */
    public function setId($id);

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}
