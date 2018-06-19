<?php

namespace Application\Model\Rest;

use Realejo\Stdlib\ArrayObject;
use Realejo\MvcUtils\ServiceLocatorTrait;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Paginator;
use ZF\Rest\AbstractResourceListener;

abstract class AbstractResource extends AbstractResourceListener
{

    use ServiceLocatorTrait;

    /**
     * @var Paginator
     */
    protected $collectionClass;

    /**
     * @var ArrayObject
     */
    protected $entityClass;

    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @var AbstractMapper
     */
    protected $mapper;

    public function __construct(AbstractMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return AbstractMapper
     */
    public function getMapper()
    {
        return $this->mapper;
    }
}
