<?php
namespace GeoNamesApi\V1\Rest\States;

use Zend\ServiceManager\ServiceManager;

class StatesResourceFactory
{
    public function __invoke(ServiceManager $services)
    {
        $orm = $services->get('doctrine.documentmanager.odm_default');
        $resource = new StatesResource();
        $resource->setOrm($orm);

        return $resource;
    }
}
