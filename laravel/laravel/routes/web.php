<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('index');
Route::redirect('/home', '/profile');
Auth::routes();

Route::middleware('auth')->group(function () {
    Route::view('/profile', 'profile')->name('profile');
    Route::get('/courses', [CourseController::class, 'index'])->name('courses');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::post('/lessons/{lesson}/complete', [LessonController::class, 'complete'])->name('lessons.complete');

    Route::middleware('admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin');
        Route::post('/admin/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::post('/admin/courses/{course}/lessons', [LessonController::class, 'store'])->name('lessons.store');
    });
});
