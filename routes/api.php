<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Routing publik
 */
Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/slug/{slug}', [BlogController::class, 'showBySlug']);

/**
 * Routing privat
 */
Route::middleware(['jwtToken', 'api'])->group(function () {

    Route::prefix('/auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('/logout', 'logout');
            Route::get('/me', 'getMe');
        });
    });

    Route::prefix('user')->controller(UserController::class)->group(function () {
        Route::get('/', 'index');
        Route::put('/update/{userId}', 'update');
        Route::delete('/delete/{userId}', 'destroy');
        Route::post('/upload-image-profile', 'uploadImageProfile');
    });

    /**
     * Group routing untuk manajemen blog
     */
    Route::prefix('blog')->controller(BlogController::class)->group(function () {
        Route::get('/id/{blogId}', 'showById');
        Route::put('/update/{blogId}', 'update');
        Route::post('/create', 'store');
        Route::delete('/delete/{blogId}', 'destroy');
    });

    /**
     * Group routing untuk manajemen komentar pada blog
     */
    Route::prefix('blog')->controller(CommentController::class)->group(function () {
        Route::get('/{blogId}/comment', 'index');
        Route::post('/{blogId}/comment', 'store');
        Route::delete('/comment/{commentId}', 'destroy');
        Route::post('/comment/{commentId}/restore', 'restore');
    });
});

/**
 * Routing fallback untuk menangani endpoint yang tidak ada
 */
Route::get('/', function () {
    abort(404, 'Endpoint Tidak Tersedia');
});

/**
 * Fallback untuk endpoint yang tidak terdaftar
 */
Route::fallback(function () {
    abort(404, 'Endpoint Tidak Tersedia');
});
