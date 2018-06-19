<?php
declare(strict_types=1);

namespace GeoNamesApi\V1\Model\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class City
 * TODO relacionamento com estados
 * @ODM\Document(collection="City")
 */
class City
{
    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="string") */
    private $name;

    /** @ODM\Field(type="string") */
    private $stateId;

    /** @ODM\Field(type="date") */
    private $createdAt;

    /** @ODM\Field(type="date") */
    private $updatedAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * @param mixed $stateId
     */
    public function setStateId($stateId): void
    {
        $this->stateId = $stateId;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt():? \datetime
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt(\Datetime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt(): \Datetime
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}