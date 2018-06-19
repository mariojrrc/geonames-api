<?php
declare(strict_types=1);
/**
 * Este aquivo serve como base para fazer a autorização do usuário
 * detentor do token aos endpoints.
 * Com ele, evita-se que um acesso ao banco de dados seja feito a cada requisição da api.
 *
 * Note que este arquivo deveria ser alterado dinamicamente através
 * de uma consulta a base de dados ou algo parecido.
 */
return [
    'b17d8756cc299c0c897454ee4dd0e58' =>
        [
            'id_apiuser' => 1,
            'name' => 'zoox',
            'status' => 'A',
            'role' => 1,
            'ip' => null,
            'permissions' =>
                [
                    'GeoNamesApi\V1\Rest\States\Controller::collection' => ['GET', 'POST', 'PUT', 'DELETE'],
                    'GeoNamesApi\V1\Rest\States\Controller::entity' => ['GET', 'POST', 'PUT', 'DELETE'],
                    'GeoNamesApi\V1\Rest\Cities\Controller::collection' => ['GET', 'POST', 'PUT', 'DELETE'],
                    'GeoNamesApi\V1\Rest\Cities\Controller::entity' => ['GET', 'POST', 'PUT', 'DELETE'],
                ],
        ],
];
