<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStateRequest;
use App\Http\Requests\UpdateStateRequest;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = State::query();

        if ($shortName = $request->query('shortName')) {
            $query->where('shortName', $shortName);
        }

        $pageSize = (int) $request->query('pageSize', 10);
        $states = $query->paginate($pageSize);

        return response()->json($states);
    }

    public function show(string $id): JsonResponse
    {
        $state = State::find($id);

        if (! $state) {
            return response()->json([
                'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                'title' => 'Not Found',
                'status' => 404,
                'detail' => 'Entity not found.',
            ], 404);
        }

        return response()->json($state);
    }

    public function store(StoreStateRequest $request): JsonResponse
    {
        State::create($request->validated());

        return response()->json(null, 201);
    }

    public function update(UpdateStateRequest $request, string $id): JsonResponse
    {
        $state = State::find($id);

        if (! $state) {
            return response()->json([
                'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                'title' => 'Not Found',
                'status' => 404,
                'detail' => 'Entity not found.',
            ], 404);
        }

        $state->update($request->validated());

        return response()->json($state);
    }

    public function destroy(string $id): JsonResponse
    {
        $state = State::find($id);

        if (! $state) {
            return response()->json([
                'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                'title' => 'Unprocessable Entity',
                'status' => 422,
                'detail' => 'Entity not found.',
            ], 422);
        }

        $state->delete();

        return response()->json(null, 204);
    }

    public function destroyAll(): JsonResponse
    {
        State::query()->delete();

        return response()->json(null, 204);
    }
}
