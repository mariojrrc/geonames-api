<?php
declare(strict_types=1);

namespace Application\Authentication\Identity;

use Application\Model\ApiUserEntity;
use ZF\MvcAuth\Identity\IdentityInterface as ZFIdentityInterface;

interface IdentityInterface extends ZFIdentityInterface
{
    public function setUser(array $apiUser, string $token): void;

    public function getApiUser(): ApiUserEntity;

    public function isAllowed(string $resource, string $privilege): bool;

    public function getResource($resource): array;

    public function hasResource($resource): bool;

    public function getToken(): string;

    public function getUserId(): int;
}
