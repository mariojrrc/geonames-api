<?php

namespace Application\Model\Rpc;

use Application\Authentication\AccessControl;
use Realejo\MvcUtils\ServiceLocatorTrait;
use Zend\Mvc\Controller\AbstractActionController;
use ZF\ApiProblem\ApiProblem;

/**
 * @method \Zend\Http\Request getRequest()
 */
abstract class AbstractController extends AbstractActionController
{
    use ServiceLocatorTrait;

    /**
     * @var AccessControl
     */
    protected $accessControl;

    /**
     * @param string $token
     * @return AccessControl|ApiProblem
     */
    public function setUsuario($token)
    {
        return $this->getAccessControl()->setUsuario($token);
    }

    /**
     * @return AccessControl
     */
    public function getAccessControl()
    {
        return $this->getFromServiceLocator('authentication')->getAccessControl();
    }

    /**
     * @return array
     */
    public function getApplicationConfig()
    {
        return $this->getServiceLocator()->get('Config');
    }

}
