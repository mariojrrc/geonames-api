<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class State extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'State';

    protected $fillable = ['name', 'shortName'];

    protected function casts(): array
    {
        return [
            'createdAt' => 'datetime',
            'updatedAt' => 'datetime',
        ];
    }

    public const CREATED_AT = 'createdAt';

    public const UPDATED_AT = 'updatedAt';
}
