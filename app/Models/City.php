<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class City extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'City';

    protected $fillable = ['name', 'stateId'];

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
