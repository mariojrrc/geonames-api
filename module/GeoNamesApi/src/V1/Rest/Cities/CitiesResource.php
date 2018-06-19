<?php
namespace GeoNamesApi\V1\Rest\Cities;

use GeoNamesApi\V1\Model\Document\City;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

use Doctrine\ODM\MongoDB\DocumentManager as ORM;

class CitiesResource extends AbstractResourceListener
{
    /**
     * @var ORM
     */
    protected $orm;
    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $newState = new City();

        $newState->setNome($data->nome);
        $newState->setEstadoId($data->estadoId);
        $newState->setDataCriacao(new \DateTime());

        $this->orm->persist($newState);
        $this->orm->flush();

        return [];
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        $object = $this->orm->find(City::class, $id);
        if (empty($object)) {
            return [];
        }
        return [
            'nome' => $object->getNome(),
            'dataCriacao' => $object->getDataCriacao(),
            'estadoId' => $object->getEstadoId(),
        ];
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        return $this->orm->getDocumentCollection(City::class)->find();
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($id, $data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Patch (partial in-place update) a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patchList($data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for collections');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    /**
     * Update a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($id, $data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }

    /**
     * @return ORM
     */
    public function getOrm(): ORM
    {
        return $this->orm;
    }

    /**
     * @param ORM $orm
     */
    public function setOrm(ORM $orm): void
    {
        $this->orm = $orm;
    }
}
