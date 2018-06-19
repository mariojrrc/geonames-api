<?php
declare(strict_types=1);

namespace Application\Authentication\Identity;

use Application\Model\ApiUserEntity;
use Zend\Permissions\Rbac\Role;

class AbstractIdentity extends Role implements IdentityInterface
{
    protected static $identity = 'abstract';

    /**
     * @var array
     */
    protected $apiUser;

    /**
     * @var array
     */
    protected $apiPermissions;

    public function __construct()
    {
        parent::__construct(static::$identity);
    }

    public function getRoleId()
    {
        return static::$identity;
    }

    public function getAuthenticationIdentity()
    {
        return null;
    }

    public function setUser(array $apiUser, string $token): void
    {
        $apiUser['token'] = $token;
        $this->apiUser = $apiUser;
        $this->apiPermissions = $this->apiUser['permissions'];
        unset($this->apiUser['permissions']);
        $this->name = $apiUser['name'];
    }

    public function getApiUser(): ApiUserEntity
    {
        return new ApiUserEntity($this->apiUser);
    }

    public function isAllowed(string $resource, string $privilege): bool
    {
        if (!$this->hasResource($resource)) {
            return false;
        }

        return (in_array($privilege, $this->apiPermissions[$resource], true));
    }

    /**
     * @param $resource
     * @return array
     */
    public function getResource($resource): array
    {
        if (!$this->hasResource($resource)) {
            return [];
        }

        $permissions = $this->apiPermissions[$resource];
        if (!is_array($permissions)) {
            $permissions = explode(',', $permissions);
        }

        return $permissions;
    }

    public function hasResource($resource): bool
    {
        return (isset($this->apiPermissions[$resource]));
    }

    public function getToken(): string
    {
        return $this->apiUser['token'];
    }

    public function getUserId(): int
    {
        return $this->apiUser['id_apiuser'];
    }
}
