<?php

declare(strict_types=1);

return [
    'b17d8756cc299c0c897454ee4dd0e58' => [
        'id_apiuser' => 1,
        'name' => 'zoox',
        'status' => 'A',
        'role' => 1,
        'ip' => null,
        'permissions' => [
            'states::collection' => ['GET', 'POST', 'PUT', 'DELETE'],
            'states::entity' => ['GET', 'POST', 'PUT', 'DELETE'],
            'cities::collection' => ['GET', 'POST', 'PUT', 'DELETE'],
            'cities::entity' => ['GET', 'POST', 'PUT', 'DELETE'],
        ],
    ],
];
