<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlanningController;

Route::get('/planning', [PlanningController::class, 'index']);
Route::post('/planning', [PlanningController::class, 'store']);
Route::get('/planning/{id}', [PlanningController::class, 'show']);
