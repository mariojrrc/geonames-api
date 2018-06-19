<?php
declare(strict_types=1);

namespace Application\Model\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Realejo\Stdlib\ArrayObject;

/** @ODM\Document(collection="User") */
class ApiUser extends ArrayObject
{
    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="string") */
    private $nome;

    /** @ODM\Field(type="string") */
    private $email;

    /** @ODM\Field(type="collection") */
    private $permissoes;

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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPermissoes()
    {
        return $this->permissoes;
    }

    /**
     * @param mixed $permissoes
     */
    public function setPermissoes($permissoes): void
    {
        $this->permissoes = $permissoes;
    }
}