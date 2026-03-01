<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $california = State::query()->create([
            'name' => 'California',
            'shortName' => 'CA',
        ]);

        $newYork = State::query()->create([
            'name' => 'New York',
            'shortName' => 'NY',
        ]);

        City::query()->create([
            'name' => 'Los Angeles',
            'stateId' => (string) $california->_id,
        ]);

        City::query()->create([
            'name' => 'San Francisco',
            'stateId' => (string) $california->_id,
        ]);

        City::query()->create([
            'name' => 'New York City',
            'stateId' => (string) $newYork->_id,
        ]);
    }
}
