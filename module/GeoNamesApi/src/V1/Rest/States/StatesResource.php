<?php
namespace GeoNamesApi\V1\Rest\States;

use GeoNamesApi\V1\Model\Document\State;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;
use Doctrine\ODM\MongoDB\DocumentManager as ORM;

class StatesResource extends AbstractResourceListener
{
    /**
     * @var ORM
     */
    private $orm;
    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $newState = new State();

        $newState->setNome($data->nome);
        $newState->setAbreviacao($data->abreviacao);
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
        return $this->orm->getDocumentCollection(State::class)->deleteIndexes();
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        $object = $this->orm->find(State::class, $id);
        if (empty($object)) {
            return [];
        }
        return [
            'nome' => $object->getNome(),
            'abreviacao' => $object->getAbreviacao(),
            'dataCriacao' => $object->getDataCriacao(),
            'cidades' => $object->getCities(),
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
        return $this->orm->getDocumentCollection(State::class)->find();
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

    public function setOrm($orm)
    {
        $this->orm = $orm;
    }
}
