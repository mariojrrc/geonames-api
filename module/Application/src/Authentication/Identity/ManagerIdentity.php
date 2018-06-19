<?php

namespace Application\Authentication\Identity;


use Application\Model\ApiUserRole;
use Application\Model\ApiUserStatus;

class ManagerIdentity extends AbstractIdentity
{
    protected static $identity = 'manager';

    public function setUser(array $apiUser, string $token): void
    {
        $this->apiUser = [
            'name' => 'API Manager',
            'token' => $token,
            'role' => ApiUserRole::USER_DEFINED,
            'status' => ApiUserStatus::ACTIVE
        ];
        $this->apiPermissions = [
            'UsuarioApi\\V1\\Rpc\\RefreshTokens\\Controller::refreshTokens' => ['GET']
        ];

        $this->name = $this->apiUser['name'];
    }
}
