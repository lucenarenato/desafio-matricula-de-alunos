<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD de Cursos - apenas admin
    Route::middleware('admin')->group(function () {
        Route::resource('cursos', CursoController::class);
        Route::resource('students', StudentController::class);
        Route::resource('registrations', RegistrationController::class);
    });
});

require __DIR__ . '/auth.php';
