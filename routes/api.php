<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CursoApiController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\RegistrationApiController;

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

// Routes público de autenticação
Route::group(['prefix' => 'auth', 'middleware' => 'api'], function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Rotas protegidas por JWT
Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Rotas protegidas de Cursos
Route::group(['middleware' => 'auth:api', 'prefix' => 'cursos'], function () {
    Route::get('/', [CursoApiController::class, 'index']);
    Route::post('/', [CursoApiController::class, 'store']);
    Route::get('/{curso}', [CursoApiController::class, 'show']);
    Route::put('/{curso}', [CursoApiController::class, 'update']);
    Route::delete('/{curso}', [CursoApiController::class, 'destroy']);
    Route::post('/bulk-delete', [CursoApiController::class, 'bulkDelete']);
});

// Rotas protegidas de Alunos
Route::group(['middleware' => 'auth:api', 'prefix' => 'students'], function () {
    Route::get('/', [StudentApiController::class, 'index']);
    Route::post('/', [StudentApiController::class, 'store']);
    Route::get('/{student}', [StudentApiController::class, 'show']);
    Route::put('/{student}', [StudentApiController::class, 'update']);
    Route::delete('/{student}', [StudentApiController::class, 'destroy']);
    Route::post('/bulk-delete', [StudentApiController::class, 'bulkDelete']);
});

// Rotas protegidas de Matrículas
Route::group(['middleware' => 'auth:api', 'prefix' => 'registrations'], function () {
    Route::get('/', [RegistrationApiController::class, 'index']);
    Route::post('/', [RegistrationApiController::class, 'store']);
    Route::get('/{registration}', [RegistrationApiController::class, 'show']);
    Route::delete('/{registration}', [RegistrationApiController::class, 'destroy']);
    Route::post('/bulk-delete', [RegistrationApiController::class, 'bulkDelete']);
});
