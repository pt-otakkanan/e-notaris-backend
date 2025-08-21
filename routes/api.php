<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua route API dikelompokkan berdasarkan fitur (Auth, Product, dll).
| Middleware juga bisa dikelompokkan agar lebih ringkas.
|--------------------------------------------------------------------------
*/

// Default route (unauthorized)
Route::get('/', function () {
    return response()->json([
        'success' => false,
        'message' => 'Tidak diizinkan mengakses. Pastikan role sudah benar atau token tidak kadaluarsa',
    ], 401);
})->name('login');

// User info jika login dengan Sanctum
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'registerUser']);
    Route::post('/verify',   [AuthController::class, 'verifyEmail']);
    Route::post('/resend',   [AuthController::class, 'resendCode']);
    Route::post('/login',    [AuthController::class, 'loginUser']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/check-user', [AuthController::class, 'checkUser'])->middleware('ability:user,penghadap,admin,notaris');
        Route::get('/check-token', [AuthController::class, 'checkToken'])->middleware('ability:penghadap,admin,notaris');
        Route::post('/logout',     [AuthController::class, 'logout']);
    });
});
/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/
Route::prefix('product')->middleware('auth:sanctum')->group(function () {
    // hanya role user/admin yang boleh melihat
    Route::middleware('ability:penghadap,notaris,admin')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{id}', [ProductController::class, 'detailProduct']);
    });

    Route::middleware('ability:penghadap,notaris')->group(function () {
        Route::post('/user/update-profile', [UserController::class, 'updateProfile']);
    });

    // hanya role admin yang boleh CRUD
    Route::middleware('ability:admin')->group(function () {
        Route::post('/', [ProductController::class, 'storeProduct']);
        Route::post('/update/{id}', [ProductController::class, 'updateProduct']);
        Route::delete('/{id}', [ProductController::class, 'destroyProduct']);
    });
});
