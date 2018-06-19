<?php
declare(strict_types=1);

namespace GeoNamesApi\V1\Rest\States;

use GeoNamesApi\V1\Model\Document\State;
use Zend\Http\Response;
use Zend\Paginator\Adapter\ArrayAdapter;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
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

        $newState->setName($data->nome);
        $newState->setShortName($data->abreviacao);
        $newState->setCreatedAt(new \DateTime());

        $this->orm->persist($newState);
        $this->orm->flush();

        // Returns default created response
        $response = new Response();
        $response->setStatusCode(201);
        return $response;
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        /**
         * @var State $object
         */
        $object = $this->orm->find(State::class, $id);
        if (empty($object)) {
            return new ApiProblemResponse(new ApiProblem(422, 'Entity not found.'));
        }

        $this->orm->remove($object);
        $this->orm->flush();

        // Returns default deleted response
        $response = new Response();
        $response->setStatusCode(204);

        return $response;
    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function deleteList($data)
    {
        $this->orm->getDocumentCollection(State::class)->remove([]);

        // Returns default deleted response
        $response = new Response();
        $response->setStatusCode(204);

        return $response;
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        /**
         * @var State $object
         */
        $object = $this->orm->find(State::class, $id);
        if (empty($object)) {
            return [];
        }
        return [
            'id' => $object->getId(),
            'nome' => $object->getName(),
            'abreviacao' => $object->getShortName(),
            'dataCriacao' => $object->getCreatedAt(),
            'dataAlteracao' => $object->getUpdatedAt(),
        ];
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function fetchAll($params = [])
    {
        $states = $this->orm->getDocumentCollection(State::class)->find($params->toArray());

        return new StatesCollection(new ArrayAdapter($states->toArray()));
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
        /**
         * @var State $object
         */
        $object = $this->orm->find(State::class, $id);
        if (empty($object)) {
            return [];
        }

        $object->setName($data->nome);
        $object->setShortName($data->abreviacao);
        $object->setUpdatedAt(new \DateTime());

        $this->orm->persist($object);
        $this->orm->flush();

        return [
            'id' => $object->getId(),
            'nome' => $object->getName(),
            'abreviacao' => $object->getShortName(),
            'dataCriacao' => $object->getCreatedAt(),
            'dataAlteracao' => $object->getUpdatedAt(),
        ];
    }

    public function setOrm($orm)
    {
        $this->orm = $orm;
    }
}
