<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AuthenticationController;
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
        Route::delete('cursos/bulk-delete', [CursoController::class, 'bulkDelete'])->name('cursos.bulkDelete');

        Route::resource('students', StudentController::class);
        Route::delete('students/bulk-delete', [StudentController::class, 'bulkDelete'])->name('students.bulkDelete');

        Route::resource('registrations', RegistrationController::class);
        Route::delete('registrations/bulk-delete', [RegistrationController::class, 'bulkDelete'])->name('registrations.bulkDelete');
    });
});

require __DIR__ . '/auth.php';
