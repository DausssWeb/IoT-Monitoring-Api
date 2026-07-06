<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;

Route::post('/sensor', [SensorController::class, 'store']);

Route::get('/latest', [SensorController::class, 'latest']);
Route::get('/trends', [SensorController::class, 'trends']);

Route::get('/logs/critical', [SensorController::class, 'logsCritical']);
