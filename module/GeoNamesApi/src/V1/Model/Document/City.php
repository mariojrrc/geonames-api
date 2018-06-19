<?php

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
    private $nome;

    /** @ODM\Field(type="string") */
    private $estadoId;

    /** @ODM\Field(type="date") */
    private $dataCriacao;

    /** @ODM\Field(type="date") */
    private $dataAlteracao;

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
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * @param $nome
     */
    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getDataAlteracao(): \datetime
    {
        return $this->dataAlteracao;
    }

    /**
     * @param mixed $dataAlteracao
     */
    public function setDataAlteracao($dataAlteracao): void
    {
        $this->dataAlteracao = $dataAlteracao;
    }

    /**
     * @return mixed
     */
    public function getDataCriacao(): \Datetime
    {
        return $this->dataCriacao;
    }

    /**
     * @param mixed $dataCriacao
     */
    public function setDataCriacao($dataCriacao): void
    {
        $this->dataCriacao = $dataCriacao;
    }

    /**
     * @return mixed
     */
    public function getEstadoId()
    {
        return $this->estadoId;
    }

    /**
     * @param mixed $estadoId
     */
    public function setEstadoId($estadoId): void
    {
        $this->estadoId = $estadoId;
    }
}