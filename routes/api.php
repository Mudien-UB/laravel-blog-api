<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Group routing untuk autentikasi (Auth)
 */

/**
 * Routing public
 */
Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/slug/{slug}', [BlogController::class, 'showBySlug']);



Route::middleware(['jwtToken', 'api'])->group(function () {

    Route::prefix('/auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            // Endpoint untuk logout
            Route::post('/logout', 'logout');

            // Endpoint untuk mendapatkan data user yang sedang login
            Route::get('/me', 'getMe');
        });
    });



    Route::prefix('user')->controller(UserController::class)->group(function () {
        Route::get('/', 'index');
        // Endpoint untuk memperbarui data user
        Route::put('/update/{userId}', 'update');

        // Endpoint untuk menghapus user
        Route::delete('/delete/{userId}', 'destroy');
    });

    /**
     * Group routing untuk blog management
     */
    Route::prefix('blog')->controller(BlogController::class)->group(function () {

        Route::get('/id/{blogId}',  'showById');
        // Endpoint untuk memperbarui blog
        Route::put('/update/{blogId}', 'update');

        // Endpoint untuk membuat blog baru
        Route::post('/create', 'store');

        // Endpoint untuk menghapus blog
        Route::delete('/delete/{blogId}', 'destroy');
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
