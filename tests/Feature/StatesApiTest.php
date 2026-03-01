<?php

namespace Tests\Feature;

use App\Models\State;
use Tests\TestCase;

class StatesApiTest extends TestCase
{
    private array $authHeaders = [
        'Authorization' => 'Geonames b17d8756cc299c0c897454ee4dd0e58',
        'Accept' => 'application/json',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        State::query()->delete();
    }

    public function test_create_state_requires_authentication(): void
    {
        $response = $this->postJson('/api/states', ['name' => 'Test', 'shortName' => 'TS']);

        $response->assertStatus(403);
    }

    public function test_create_state_validation_fails_with_empty_fields(): void
    {
        $response = $this->postJson('/api/states', [], $this->authHeaders);

        $response->assertStatus(422);
        $response->assertJsonPath('detail', 'Failed Validation');
        $response->assertJsonStructure(['validation_messages' => ['name', 'shortName']]);
    }

    public function test_create_state_success(): void
    {
        $response = $this->postJson('/api/states', [
            'name' => 'São Paulo',
            'shortName' => 'SP',
        ], $this->authHeaders);

        $response->assertStatus(201);
    }

    public function test_create_state_short_name_must_be_2_chars(): void
    {
        $response = $this->postJson('/api/states', [
            'name' => 'São Paulo',
            'shortName' => 'SPP',
        ], $this->authHeaders);

        $response->assertStatus(422);
    }

    public function test_create_state_name_min_3_chars(): void
    {
        $response = $this->postJson('/api/states', [
            'name' => 'AB',
            'shortName' => 'SP',
        ], $this->authHeaders);

        $response->assertStatus(422);
    }

    public function test_list_states(): void
    {
        State::create(['name' => 'São Paulo', 'shortName' => 'SP']);
        State::create(['name' => 'Rio de Janeiro', 'shortName' => 'RJ']);

        $response = $this->getJson('/api/states', $this->authHeaders);

        $response->assertStatus(200);
        $response->assertJsonPath('total', 2);
        $response->assertJsonCount(2, 'data');
    }

    public function test_list_states_filter_by_short_name(): void
    {
        State::create(['name' => 'São Paulo', 'shortName' => 'SP']);
        State::create(['name' => 'Rio de Janeiro', 'shortName' => 'RJ']);

        $response = $this->getJson('/api/states?shortName=SP', $this->authHeaders);

        $response->assertStatus(200);
        $response->assertJsonPath('total', 1);
        $response->assertJsonPath('data.0.shortName', 'SP');
    }

    public function test_show_state(): void
    {
        $state = State::create(['name' => 'São Paulo', 'shortName' => 'SP']);

        $response = $this->getJson('/api/states/'.$state->_id, $this->authHeaders);

        $response->assertStatus(200);
        $response->assertJsonPath('name', 'São Paulo');
        $response->assertJsonPath('shortName', 'SP');
    }

    public function test_show_state_not_found(): void
    {
        $response = $this->getJson('/api/states/nonexistent', $this->authHeaders);

        $response->assertStatus(404);
    }

    public function test_update_state(): void
    {
        $state = State::create(['name' => 'São Paulo', 'shortName' => 'SP']);

        $response = $this->putJson('/api/states/'.$state->_id, [
            'name' => 'Minas Gerais',
            'shortName' => 'MG',
        ], $this->authHeaders);

        $response->assertStatus(200);
        $response->assertJsonPath('name', 'Minas Gerais');
        $response->assertJsonPath('shortName', 'MG');
    }

    public function test_delete_state(): void
    {
        $state = State::create(['name' => 'São Paulo', 'shortName' => 'SP']);

        $response = $this->deleteJson('/api/states/'.$state->_id, [], $this->authHeaders);

        $response->assertStatus(204);
        $this->assertNull(State::find($state->_id));
    }

    public function test_delete_all_states(): void
    {
        State::create(['name' => 'São Paulo', 'shortName' => 'SP']);
        State::create(['name' => 'Rio de Janeiro', 'shortName' => 'RJ']);

        $response = $this->deleteJson('/api/states', [], $this->authHeaders);

        $response->assertStatus(204);
        $this->assertEquals(0, State::count());
    }

    public function test_short_name_is_uppercased(): void
    {
        $response = $this->postJson('/api/states', [
            'name' => 'São Paulo',
            'shortName' => 'sp',
        ], $this->authHeaders);

        $response->assertStatus(201);

        $state = State::first();
        $this->assertEquals('SP', $state->shortName);
    }
}
