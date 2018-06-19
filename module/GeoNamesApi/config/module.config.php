<?php
return [
    'service_manager' => [
        'factories' => [
            \GeoNamesApi\V1\Rest\States\StatesResource::class => \GeoNamesApi\V1\Rest\States\StatesResourceFactory::class,
            \GeoNamesApi\V1\Rest\Cities\CitiesResource::class => \GeoNamesApi\V1\Rest\Cities\CitiesResourceFactory::class,
        ],
    ],
    'doctrine' => [
        'driver' => [
            '_driver' => [
                'class' => \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::class,
                'paths' => [
                    0 => __DIR__ . '/../src/V1/Model/Document',
                ],
            ],
            'odm_default' => [
                'drivers' => [
                    'GeoNamesApi\\V1\\Model\\Document' => '_driver',
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'geo-names-api.rest.states' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/states[/:states_id]',
                    'defaults' => [
                        'controller' => 'GeoNamesApi\\V1\\Rest\\States\\Controller',
                    ],
                ],
            ],
            'geo-names-api.rest.cities' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/cities[/:cities_id]',
                    'defaults' => [
                        'controller' => 'GeoNamesApi\\V1\\Rest\\Cities\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'geo-names-api.rest.states',
            1 => 'geo-names-api.rest.cities',
        ],
    ],
    'zf-rest' => [
        'GeoNamesApi\\V1\\Rest\\States\\Controller' => [
            'listener' => \GeoNamesApi\V1\Rest\States\StatesResource::class,
            'route_name' => 'geo-names-api.rest.states',
            'route_identifier_name' => 'states_id',
            'collection_name' => 'states',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
                2 => 'DELETE',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => 'page_size',
            'entity_class' => \GeoNamesApi\V1\Rest\States\StatesEntity::class,
            'collection_class' => \GeoNamesApi\V1\Rest\States\StatesCollection::class,
            'service_name' => 'States',
        ],
        'GeoNamesApi\\V1\\Rest\\Cities\\Controller' => [
            'listener' => \GeoNamesApi\V1\Rest\Cities\CitiesResource::class,
            'route_name' => 'geo-names-api.rest.cities',
            'route_identifier_name' => 'cities_id',
            'collection_name' => 'cities',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => 'page_size',
            'entity_class' => \GeoNamesApi\V1\Rest\Cities\CitiesEntity::class,
            'collection_class' => \GeoNamesApi\V1\Rest\Cities\CitiesCollection::class,
            'service_name' => 'Cities',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'GeoNamesApi\\V1\\Rest\\States\\Controller' => 'HalJson',
            'GeoNamesApi\\V1\\Rest\\Cities\\Controller' => 'HalJson',
        ],
        'accept_whitelist' => [
            'GeoNamesApi\\V1\\Rest\\States\\Controller' => [
                0 => 'application/vnd.geo-names-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'GeoNamesApi\\V1\\Rest\\Cities\\Controller' => [
                0 => 'application/vnd.geo-names-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content_type_whitelist' => [
            'GeoNamesApi\\V1\\Rest\\States\\Controller' => [
                0 => 'application/vnd.geo-names-api.v1+json',
                1 => 'application/json',
            ],
            'GeoNamesApi\\V1\\Rest\\Cities\\Controller' => [
                0 => 'application/vnd.geo-names-api.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \GeoNamesApi\V1\Rest\States\StatesEntity::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'geo-names-api.rest.states',
                'route_identifier_name' => 'states_id',
                'hydrator' => \DoctrineModule\Stdlib\Hydrator\DoctrineObject::class,
            ],
            \GeoNamesApi\V1\Rest\States\StatesCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'geo-names-api.rest.states',
                'route_identifier_name' => 'states_id',
                'is_collection' => true,
            ],
            \GeoNamesApi\V1\Rest\Cities\CitiesEntity::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'geo-names-api.rest.cities',
                'route_identifier_name' => 'cities_id',
                'hydrator' => \DoctrineModule\Stdlib\Hydrator\DoctrineObject::class,
            ],
            \GeoNamesApi\V1\Rest\Cities\CitiesCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'geo-names-api.rest.cities',
                'route_identifier_name' => 'cities_id',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-mvc-auth' => [
        'authorization' => [
            'GeoNamesApi\\V1\\Rest\\States\\Controller' => [
                'collection' => [
                    'GET' => false,
                    'POST' => false,
                    'PUT' => false,
                    'PATCH' => false,
                    'DELETE' => false,
                ],
                'entity' => [
                    'GET' => false,
                    'POST' => false,
                    'PUT' => false,
                    'PATCH' => false,
                    'DELETE' => false,
                ],
            ],
            'GeoNamesApi\\V1\\Rest\\Cities\\Controller' => [
                'collection' => [
                    'GET' => false,
                    'POST' => false,
                    'PUT' => false,
                    'PATCH' => false,
                    'DELETE' => false,
                ],
                'entity' => [
                    'GET' => true,
                    'POST' => false,
                    'PUT' => true,
                    'PATCH' => true,
                    'DELETE' => true,
                ],
            ],
        ],
    ],
    'zf-content-validation' => [
        'GeoNamesApi\\V1\\Rest\\States\\Controller' => [
            'input_filter' => 'GeoNamesApi\\V1\\Rest\\States\\Validator',
        ],
        'GeoNamesApi\\V1\\Rest\\Cities\\Controller' => [
            'input_filter' => 'GeoNamesApi\\V1\\Rest\\Cities\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'GeoNamesApi\\V1\\Rest\\States\\Validator' => [
            0 => [
                'required' => true,
                'validators' => [
                    0 => [
                        'name' => \Zend\Validator\StringLength::class,
                        'options' => [
                            'max' => '100',
                            'min' => '3',
                        ],
                    ],
                ],
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                        'options' => [],
                    ],
                    1 => [
                        'name' => \Zend\Filter\StripNewlines::class,
                        'options' => [],
                    ],
                    2 => [
                        'name' => \Zend\Filter\StripTags::class,
                        'options' => [],
                    ],
                ],
                'name' => 'nome',
                'field_type' => 'string',
            ],
            1 => [
                'required' => true,
                'validators' => [
                    0 => [
                        'name' => \Zend\Validator\StringLength::class,
                        'options' => [
                            'min' => '2',
                            'max' => '2',
                        ],
                    ],
                ],
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                        'options' => [],
                    ],
                    1 => [
                        'name' => \Zend\Filter\StripNewlines::class,
                        'options' => [],
                    ],
                    2 => [
                        'name' => \Zend\Filter\StripTags::class,
                        'options' => [],
                    ],
                    3 => [
                        'name' => \Zend\Filter\StringToUpper::class,
                        'options' => [
                            'encoding' => 'utf-8',
                        ],
                    ],
                ],
                'name' => 'abreviacao',
                'field_type' => 'string',
            ],
        ],
        'GeoNamesApi\\V1\\Rest\\Cities\\Validator' => [
            0 => [
                'required' => true,
                'validators' => [
                    0 => [
                        'name' => \Zend\Validator\StringLength::class,
                        'options' => [
                            'max' => '100',
                            'min' => '3',
                        ],
                    ],
                ],
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                        'options' => [],
                    ],
                    1 => [
                        'name' => \Zend\Filter\StripNewlines::class,
                        'options' => [],
                    ],
                    2 => [
                        'name' => \Zend\Filter\StripTags::class,
                        'options' => [],
                    ],
                ],
                'name' => 'nome',
                'field_type' => 'string',
            ],
            1 => [
                'required' => true,
                'validators' => [],
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                        'options' => [],
                    ],
                    1 => [
                        'name' => \Zend\Filter\StripTags::class,
                        'options' => [],
                    ],
                ],
                'name' => 'estadoId',
                'field_type' => 'string',
            ],
        ],
    ],
];
