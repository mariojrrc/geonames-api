<?php
declare(strict_types=1);

namespace GeoNamesApi\V1\Model\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
/**
 * Class State
 * TODO relacionamento com cidades
 *
 * @ODM\Document(collection="State")
 */
class State
{
    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="string") */
    private $name;

    /** @ODM\Field(type="string") */
    private $shortName;

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
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt(): \Datetime
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
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

    /**
     * @return mixed
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @param mixed $shortName
     */
    public function setShortName($shortName): void
    {
        $this->shortName = $shortName;
    }
}