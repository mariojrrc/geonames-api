<?php

namespace Application\Model\Rpc;

use Realejo\MvcUtils\ServiceLocatorTrait;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * @method \Zend\Http\Request getRequest()
 */
abstract class AbstractController extends AbstractActionController
{
    use ServiceLocatorTrait;

    /**
     * @return array
     */
    public function getApplicationConfig()
    {
        return $this->getServiceLocator()->get('Config');
    }

}
