<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\State;
use Tests\TestCase;

class CitiesApiTest extends TestCase
{
    private array $authHeaders = [
        'Authorization' => 'Geonames b17d8756cc299c0c897454ee4dd0e58',
        'Accept' => 'application/json',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        City::query()->delete();
        State::query()->delete();
    }

    public function test_create_city_requires_authentication(): void
    {
        $response = $this->postJson('/api/cities', ['name' => 'Test', 'stateId' => 'abc']);

        $response->assertStatus(403);
    }

    public function test_create_city_validation_fails_with_empty_fields(): void
    {
        $response = $this->postJson('/api/cities', [], $this->authHeaders);

        $response->assertStatus(422);
        $response->assertJsonPath('detail', 'Failed Validation');
        $response->assertJsonStructure(['validation_messages' => ['name', 'stateId']]);
    }

    public function test_create_city_success(): void
    {
        $state = State::create(['name' => 'São Paulo', 'shortName' => 'SP']);

        $response = $this->postJson('/api/cities', [
            'name' => 'São Paulo',
            'stateId' => (string) $state->_id,
        ], $this->authHeaders);

        $response->assertStatus(201);
    }

    public function test_list_cities(): void
    {
        $state = State::create(['name' => 'São Paulo', 'shortName' => 'SP']);

        City::create(['name' => 'São Paulo', 'stateId' => (string) $state->_id]);
        City::create(['name' => 'Campinas', 'stateId' => (string) $state->_id]);

        $response = $this->getJson('/api/cities', $this->authHeaders);

        $response->assertStatus(200);
        $response->assertJsonPath('total', 2);
        $response->assertJsonCount(2, 'data');
    }

    public function test_list_cities_filter_by_state_id(): void
    {
        $sp = State::create(['name' => 'São Paulo', 'shortName' => 'SP']);
        $rj = State::create(['name' => 'Rio de Janeiro', 'shortName' => 'RJ']);

        City::create(['name' => 'São Paulo', 'stateId' => (string) $sp->_id]);
        City::create(['name' => 'Rio de Janeiro', 'stateId' => (string) $rj->_id]);

        $response = $this->getJson('/api/cities?stateId='.$sp->_id, $this->authHeaders);

        $response->assertStatus(200);
        $response->assertJsonPath('total', 1);
        $response->assertJsonPath('data.0.name', 'São Paulo');
    }

    public function test_show_city(): void
    {
        $state = State::create(['name' => 'São Paulo', 'shortName' => 'SP']);
        $city = City::create(['name' => 'São Paulo', 'stateId' => (string) $state->_id]);

        $response = $this->getJson('/api/cities/'.$city->_id, $this->authHeaders);

        $response->assertStatus(200);
        $response->assertJsonPath('name', 'São Paulo');
    }

    public function test_update_city(): void
    {
        $state = State::create(['name' => 'São Paulo', 'shortName' => 'SP']);
        $city = City::create(['name' => 'São Paulo', 'stateId' => (string) $state->_id]);

        $response = $this->putJson('/api/cities/'.$city->_id, [
            'name' => 'Campinas',
        ], $this->authHeaders);

        $response->assertStatus(200);
        $response->assertJsonPath('name', 'Campinas');
    }

    public function test_delete_city(): void
    {
        $state = State::create(['name' => 'São Paulo', 'shortName' => 'SP']);
        $city = City::create(['name' => 'São Paulo', 'stateId' => (string) $state->_id]);

        $response = $this->deleteJson('/api/cities/'.$city->_id, [], $this->authHeaders);

        $response->assertStatus(204);
        $this->assertNull(City::find($city->_id));
    }

    public function test_delete_all_cities(): void
    {
        $state = State::create(['name' => 'São Paulo', 'shortName' => 'SP']);
        City::create(['name' => 'São Paulo', 'stateId' => (string) $state->_id]);
        City::create(['name' => 'Campinas', 'stateId' => (string) $state->_id]);

        $response = $this->deleteJson('/api/cities', [], $this->authHeaders);

        $response->assertStatus(204);
        $this->assertEquals(0, City::count());
    }
}
