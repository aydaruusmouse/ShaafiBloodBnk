<?php

use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\HospitalController;
use App\Http\Controllers\Api\V1\ShaafiRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('shaafi.api')->group(function () {
    Route::get('/cities', [CityController::class, 'index']);
    Route::get('/hospitals', [HospitalController::class, 'index']);
    Route::post('/requests', [ShaafiRequestController::class, 'store']);
});
