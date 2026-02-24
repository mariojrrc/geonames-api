<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = City::query();

        if ($stateId = $request->query('stateId')) {
            $query->where('stateId', $stateId);
        }

        $pageSize = (int) $request->query('pageSize', 10);
        $cities = $query->paginate($pageSize);

        return response()->json($cities);
    }

    public function show(string $id): JsonResponse
    {
        $city = City::find($id);

        if (! $city) {
            return response()->json([
                'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                'title' => 'Not Found',
                'status' => 404,
                'detail' => 'Entity not found.',
            ], 404);
        }

        return response()->json($city);
    }

    public function store(StoreCityRequest $request): JsonResponse
    {
        City::create($request->validated());

        return response()->json(null, 201);
    }

    public function update(UpdateCityRequest $request, string $id): JsonResponse
    {
        $city = City::find($id);

        if (! $city) {
            return response()->json([
                'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                'title' => 'Not Found',
                'status' => 404,
                'detail' => 'Entity not found.',
            ], 404);
        }

        $city->update($request->validated());

        return response()->json($city);
    }

    public function destroy(string $id): JsonResponse
    {
        $city = City::find($id);

        if (! $city) {
            return response()->json([
                'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                'title' => 'Unprocessable Entity',
                'status' => 422,
                'detail' => 'Entity not found.',
            ], 422);
        }

        $city->delete();

        return response()->json(null, 204);
    }

    public function destroyAll(): JsonResponse
    {
        City::query()->delete();

        return response()->json(null, 204);
    }
}
