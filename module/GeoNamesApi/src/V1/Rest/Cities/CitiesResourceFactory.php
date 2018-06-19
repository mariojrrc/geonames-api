<?php

namespace GeoNamesApi\V1\Rest\Cities;

use Zend\ServiceManager\ServiceManager;

class CitiesResourceFactory
{
    public function __invoke(ServiceManager $services)
    {
        $orm = $services->get('doctrine.documentmanager.odm_default');
        $resource = new CitiesResource();
        $resource->setOrm($orm);

        return $resource;
    }
}
