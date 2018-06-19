<?php
declare(strict_types=1);

namespace Application\Model;

use Realejo\Enum\Enum;

class ApiUserStatus extends Enum
{
    const BLOCKED = 'B';
    const ACTIVE = 'A';

    static protected $constDescription = [
        self::BLOCKED => 'Bloqueado',
        self::ACTIVE => 'Ativo',
    ];
}
