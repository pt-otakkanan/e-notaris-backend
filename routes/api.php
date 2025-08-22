<?php

use App\Http\Controllers\NotarisActivityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeedController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VerificationController;

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
});
// hanya role admin yang boleh CRUD
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [ProductController::class, 'storeProduct']);
    Route::post('/update/{id}', [ProductController::class, 'updateProduct']);
    Route::delete('/{id}', [ProductController::class, 'destroyProduct']);

    Route::prefix('verification')->middleware('ability:admin')->group(function () {
        Route::post('/identity', [VerificationController::class, 'verifyIdentity']);
        Route::get('/users-pending', [VerificationController::class, 'getPendingVerifications']);
        Route::get('/users-rejected', [VerificationController::class, 'getRejectVerifications']);
        Route::get('/users-approved', [VerificationController::class, 'getApprovedVerifications']);
        Route::get('/users-rejected-pending', [VerificationController::class, 'getRejectedPendingVerifications']);
        Route::get('/users', [VerificationController::class, 'getAllUsers']);
        Route::get('/users/{id}', [VerificationController::class, 'getUserDetail']);
    });
    Route::prefix('deed')->middleware('ability:admin')->group(function () {
        Route::get('/',        [DeedController::class, 'index'])->middleware('ability:admin');
        Route::get('/{id}',    [DeedController::class, 'show'])->middleware('ability:admin');
        Route::post('/',       [DeedController::class, 'store'])->middleware('ability:admin');
        Route::post('/update/{id}',    [DeedController::class, 'update'])->middleware('ability:admin');
        Route::delete('/{id}', [DeedController::class, 'destroy'])->middleware('ability:admin');
    });
    Route::prefix('user')->middleware('ability:admin')->group(function () {
        Route::get('/',        [UserController::class, 'getAllUsers']);
        Route::get('/{id}',    [UserController::class, 'getDetailUser']);
        Route::delete('/{id}', [UserController::class, 'destroyUser']);
    });
});
Route::prefix('notaris')->middleware('auth:sanctum')->group(function () {
    // hanya role user/admin yang boleh melihat
    Route::prefix('activity')->middleware('ability:notaris')->group(function () {
        Route::get('/',        [NotarisActivityController::class, 'index']);
        Route::get('/{id}',    [NotarisActivityController::class, 'show']);
        Route::post('/',       [NotarisActivityController::class, 'store']);
        Route::post('/update/{id}',    [NotarisActivityController::class, 'update']);
    });
});
Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    // hanya role user/admin yang boleh melihat
    Route::middleware('ability:penghadap,notaris,admin')->group(function () {
        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::post('/update-profile', [UserController::class, 'updateProfile']);
        Route::post('/update-identity-profile', [UserController::class, 'updateIdentityProfile']);
    });
});
