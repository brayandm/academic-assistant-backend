<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1'], function () {

    Route::post('/login', 'App\Http\Controllers\AuthController@login');

    // Route::post('/register', 'App\Http\Controllers\AuthController@register');

    Route::middleware(['check.constant.connection', 'auth:sanctum'])->group(function () {

        Route::get('/verify', 'App\Http\Controllers\AuthController@verify');

        Route::post('/logout', 'App\Http\Controllers\AuthController@logout');

        Route::post('/logoutall', 'App\Http\Controllers\AuthController@logoutall');

        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });

    Route::middleware(['engine.auth', 'engine.usage'])->group(function () {

        Route::post('/webhook/engine/translate', 'App\Http\Controllers\EngineController@webhookTranslate')->name('engine.webhook.translate');
    });

    Route::middleware(['streamer.auth'])->group(function () {

        Route::post('/streamer/task/access-control', 'App\Http\Controllers\StreamerController@accessControl')->name('streamer.task.access.control');
        Route::post('/streamer/task/create', 'App\Http\Controllers\StreamerController@createTask')->name('streamer.task.create');
    });
});
