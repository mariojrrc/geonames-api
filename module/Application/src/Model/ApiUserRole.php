<?php

namespace Application\Model;

use Realejo\Enum\EnumFlagged;

class ApiUserRole extends EnumFlagged
{
    const USER_DEFINED = 1 << 0; // 1
    const STORE = 1 << 1; // 2

    static protected $constDescription = [
        self::USER_DEFINED => 'User Defined',
        self::STORE => 'Store',
    ];
}
