<?php

use App\Http\Controllers\Api\ElectionApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API endpoints
Route::prefix('v1')->group(function () {
    Route::get('/elections', [ElectionApiController::class, 'index']);
    Route::get('/elections/{election}', [ElectionApiController::class, 'show']);
    Route::get('/elections/{election}/results', [ElectionApiController::class, 'results']);
    Route::get('/statistics', [ElectionApiController::class, 'statistics']);
});

// Protected API endpoints
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/my-votes', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()->votes()->with('election', 'candidate')->get(),
        ]);
    });
});
