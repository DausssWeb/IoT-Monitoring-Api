<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;

Route::get('/', [SensorController::class, 'dashboard']);
Route::get('/logs', [SensorController::class, 'logsPage']);
Route::get('/export-pdf', [SensorController::class, 'exportPdf']);

