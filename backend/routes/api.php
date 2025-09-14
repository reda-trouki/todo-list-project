<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

// Broadcasting authentication
Route::middleware('auth:api')->post('/broadcasting/auth', function (Request $request) {
    $user = Auth::guard('api')->user();
    \Log::info('Broadcasting auth request:', [
        'user_id' => $user ? $user->id : 'null',
        'channel_name' => $request->input('channel_name'),
        'socket_id' => $request->input('socket_id'),
        'headers' => $request->headers->all()
    ]);

    try {
        $result = \Illuminate\Support\Facades\Broadcast::auth($request);
        \Log::info('Broadcasting auth successful');
        return $result;
    } catch (\Exception $e) {
        \Log::error('Broadcasting auth failed:', ['error' => $e->getMessage()]);
        throw $e;
    }
});

// Protected task routes
Route::middleware('auth:api')->group(function () {
    Route::apiResource('tasks', TaskController::class);
    Route::get('users', [TaskController::class, 'getUsers']);
});
