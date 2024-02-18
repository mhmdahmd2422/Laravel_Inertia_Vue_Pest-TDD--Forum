<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TemporaryImageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::post('/upload', [TemporaryImageController::class, 'store']);
    Route::delete('/revert/{fileName}', [TemporaryImageController::class, 'destroy']);
    Route::delete('/image/{image}', [CommentImageController::class, 'destroy'])
        ->name('image.destroy');
    Route::resource('posts.comments', CommentController::class)
        ->shallow()->only(['store', 'update', 'destroy']);
    Route::resource('posts', PostController::class)->only(['store', 'create']);
});

Route::get('posts/{post}/{slug}', [PostController::class, 'show'])
    ->name('posts.show');
Route::resource('posts' , PostController::class)->only(['index']);
