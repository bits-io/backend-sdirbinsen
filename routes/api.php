<?php

use App\Http\Controllers\Api\V1\Admin\ELearningController;
use App\Http\Controllers\Api\V1\Admin\MaterialController;
use App\Http\Controllers\Api\V1\Admin\PersonilController;
use App\Http\Controllers\Api\V1\AuthController;

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

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'masterLogin']);


    Route::group(['prefix' => 'admin', 'middleware' => ['jwt.role:1']], function () {

        Route::get('personil', [PersonilController::class, 'index']);
        Route::get('personil/{id}', [PersonilController::class, 'show']);
        Route::put('personil/{id}', [PersonilController::class, 'update']);
        Route::post('personil', [PersonilController::class, 'store']);
        Route::delete('personil/{id}', [PersonilController::class, 'destroy']);

        Route::get('e-learning', [ELearningController::class, 'index']);
        Route::get('e-learning/{id}', [ELearningController::class, 'show']);
        Route::put('e-learning/{id}', [ELearningController::class, 'update']);
        Route::post('e-learning', [ELearningController::class, 'store']);
        Route::delete('e-learning/{id}', [ELearningController::class, 'destroy']);

        Route::get('material', [MaterialController::class, 'index']);
        Route::get('material/{id}', [MaterialController::class, 'show']);
        Route::put('material/{id}', [MaterialController::class, 'update']);
        Route::post('material', [MaterialController::class, 'store']);
        Route::delete('material/{id}', [MaterialController::class, 'destroy']);

    });
});
