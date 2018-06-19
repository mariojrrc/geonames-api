<?php
declare(strict_types=1);

namespace GeoNamesApi\V1\Rest\Cities;

use GeoNamesApi\V1\Model\Document\City;
use Zend\Http\Response;
use Zend\Paginator\Adapter\ArrayAdapter;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
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
        $newCity = new City();

        $newCity->setName($data->nome);
        $newCity->setStateId($data->estadoId);
        $newCity->setCreatedAt(new \DateTime());

        $this->orm->persist($newCity);
        $this->orm->flush();

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
         * @var City $object
         */
        $object = $this->orm->find(City::class, $id);
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
        $this->orm->getDocumentCollection(City::class)->remove([]);

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
         * @var City $object
         */
        $object = $this->orm->find(City::class, $id);
        if (empty($object)) {
            return [];
        }

        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'stateId' => $object->getStateId(),
            'createdAt' => $object->getCreatedAt(),
            'updatedAt' => $object->getUpdatedAt(),
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
        $cities = $this->orm->getDocumentCollection(City::class)->find($params->toArray());

        return new CitiesCollection(new ArrayAdapter($cities->toArray()));
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
         * @var City $object
         */
        $object = $this->orm->find(City::class, $id);
        if (empty($object)) {
            return [];
        }

        $object->setName($data->nome);
        $object->setStateId($data->estadoId);
        $object->setUpdatedAt(new \DateTime());

        $this->orm->persist($object);
        $this->orm->flush();

        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'stateId' => $object->getStateId(),
            'createdAt' => $object->getCreatedAt(),
            'updatedAt' => $object->getUpdatedAt(),
        ];
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
