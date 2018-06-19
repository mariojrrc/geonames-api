<?php
declare(strict_types=1);

use Zend\Db\Adapter\AdapterServiceFactory;
use Application\Authentication\AuthenticationServiceFactory;

return [
    'router' => [
        'routes' => [
            'oauth' => [
                'options' => [
                    'spec' => '%oauth%',
                    'regex' => '(?P<oauth>(/login))',
                ],
                'type' => 'regex',
            ],
        ],
    ],
    'backend' => [
        'api' => [
            'manager' => 'Insert the manager token here'
        ]
    ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => AdapterServiceFactory::class,
            'ZF\MvcAuth\Authentication' => AuthenticationServiceFactory::class
        ],
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        ],
    ],
    'zf-apigility-admin' => [
        'path_spec' => 'psr-4'
    ],
    'zf-configuration' => [
        'enable_short_array' => true,
        'class_name_scalars' => true,
    ],
];
