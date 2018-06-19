<?php
declare(strict_types=1);

namespace Application\Model\Rest;

use Realejo\MvcUtils\ServiceLocatorTrait;
use Realejo\Stdlib\ArrayObject;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Paginator;

/**
 * @method ArrayObject[] fetchAll(array $where = null, $order = null, $offset = null, $limit = null)
 * @method ArrayObject[] fetchAssoc(array $where = null, $order = null, $offset = null, $limit = null)
 * @method ArrayObject fetchRow($where = null, $order = null)
 */
abstract class AbstractMapper
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
     * AbstractMapper constructor.
     * @param TableGateway $tableGateway
     * @param $entityClass
     * @param $collectionClass
     */
    public function __construct(
        TableGateway $tableGateway,
        $entityClass,
        $collectionClass
    ) {

        $this->tableGateway = $tableGateway;
        $this->entityClass = $entityClass;
        $this->collectionClass = $collectionClass;
    }
}
