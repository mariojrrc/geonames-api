<?php
declare(strict_types=1);

namespace Application\Authentication\Identity;

class UserDefinedIdentity extends AbstractIdentity
{
    protected static $identity = 'user-defined';
}
