<?php

use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\StateController;
use App\Http\Middleware\TokenAuthentication;
use Illuminate\Support\Facades\Route;

Route::middleware(TokenAuthentication::class)->group(function () {
    Route::delete('/states', [StateController::class, 'destroyAll']);
    Route::apiResource('states', StateController::class);

    Route::delete('/cities', [CityController::class, 'destroyAll']);
    Route::apiResource('cities', CityController::class);
});
