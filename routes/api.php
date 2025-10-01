<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DeedController;
use App\Http\Controllers\SignController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientDraftController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\CategoryBlogController;
use App\Http\Controllers\EditorUploadController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\MainValueDeedController;
use App\Http\Controllers\ClientActivityController;
use App\Http\Controllers\NotarisActivityController;
use App\Http\Controllers\DocumentRequirementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Disusun per area: public → auth → admin → notaris → user → shared.
| Middleware disatukan agar tidak berulang, nama path diseragamkan.
|--------------------------------------------------------------------------
*/

// -------------------------------------------------------------
// Root (unauthorized)
// -------------------------------------------------------------
Route::get('/', function () {
    return response()->json([
        'success' => false,
        'message' => 'Tidak diizinkan mengakses. Pastikan role sudah benar atau token tidak kadaluarsa',
    ], 401);
})->name('login');

// -------------------------------------------------------------
// Auth (tanpa login & dengan login)
// -------------------------------------------------------------
Route::prefix('auth')->group(function () {
    // Public
    Route::post('/register', [AuthController::class, 'registerUser']);
    Route::post('/verify',   [AuthController::class, 'verifyEmail']);
    Route::post('/resend',   [AuthController::class, 'resendCode']);
    Route::post('/login',    [AuthController::class, 'loginUser']);

    // Protected
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/check-user',  [AuthController::class, 'checkUser'])->middleware('ability:user,penghadap,admin,notaris');
        Route::get('/check-token', [AuthController::class, 'checkToken'])->middleware('ability:penghadap,admin,notaris');
        Route::post('/logout',     [AuthController::class, 'logout']);
    });
});

// Info user yang login (sekadar util umum)
Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());

/*
|--------------------------------------------------------------------------
| Shared (All Authenticated Roles): read-only endpoints yang dipakai semua role
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Produk (list)
    Route::prefix('product')->middleware('ability:penghadap,notaris,admin')->group(function () {
        Route::get('/',          [ProductController::class, 'index']);
        Route::get('/schedule',  [ProductController::class, 'index']); // (tetap dipertahankan kalau memang dipakai FE)
    });

    // Schedule untuk user (kalender pengguna)
    Route::middleware('ability:penghadap,notaris,admin')->group(function () {
        Route::get('/schedule/user',     [ScheduleController::class, 'allScheduleUser']);
        Route::get('/schedule/user/{id}', [ScheduleController::class, 'show']);
    });

    // Track lookup via tracking code → boleh untuk semua user login
    Route::prefix('track')->group(function () {
        Route::get('/lookup/{code}', [TrackController::class, 'lookupByCode']);
        Route::post('/lookup',       [TrackController::class, 'lookupByCodePost']);
    });
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'getData']);
    });
});

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {

    // Verification (khusus admin)
    Route::prefix('verification')->middleware('ability:admin')->group(function () {
        Route::post('/identity',                [VerificationController::class, 'verifyIdentity']);
        Route::get('/users-pending',            [VerificationController::class, 'getPendingVerifications']);
        Route::get('/users-rejected',           [VerificationController::class, 'getRejectVerifications']);
        Route::get('/users-approved',           [VerificationController::class, 'getApprovedVerifications']);
        Route::get('/users-rejected-pending',   [VerificationController::class, 'getRejectedPendingVerifications']);
        Route::get('/users',                    [VerificationController::class, 'getAllUsers']);
        Route::get('/users/{id}',               [UserController::class, 'getDetailUser']);
    });

    // Templates (by admin)
    Route::prefix('templates')->middleware('ability:admin,notaris')->group(function () {
        Route::get('/',                 [TemplateController::class, 'index']);
        Route::get('/{id}',             [TemplateController::class, 'show']);
        Route::post('/',                [TemplateController::class, 'store']);
        Route::post('/update/{id}',     [TemplateController::class, 'update']);
        Route::delete('/{id}',          [TemplateController::class, 'destroy']);
        Route::get('/all/template',     [TemplateController::class, 'all']);
        Route::post('/import-docx', [TemplateController::class, 'importDocx']);
        Route::post('/{id}/render-pdf', [TemplateController::class, 'renderPdf']);
    });

    Route::prefix('blogs')->middleware('ability:admin')->group(function () {
        Route::get('/',                 [BlogController::class, 'index']);
        Route::get('/all/blog',         [BlogController::class, 'all']);
        Route::get('/{id}',             [BlogController::class, 'show']);
        Route::post('/',                [BlogController::class, 'store']);
        Route::post('/update/{id}',     [BlogController::class, 'update']);
        Route::delete('/{id}',          [BlogController::class, 'destroy']);
    });

    Route::prefix('partners')->middleware('ability:admin')->group(function () {
        Route::get('/',                 [PartnerController::class, 'index']);
        Route::get('/all/partner',      [PartnerController::class, 'all']);
        Route::get('/{id}',             [PartnerController::class, 'show']);
        Route::post('/',                [PartnerController::class, 'store']);
        Route::post('/update/{id}',     [PartnerController::class, 'update']);
        Route::delete('/{id}',          [PartnerController::class, 'destroy']);
    });

    Route::prefix('settings')->middleware('ability:admin')->group(function () {
        Route::get('/',        [SettingController::class, 'get']);
        Route::post('/',       [SettingController::class, 'upsert']);
    });

    Route::prefix('category-blogs')->middleware('ability:admin')->group(function () {
        Route::get('/',             [CategoryBlogController::class, 'index']);
        Route::get('/all',          [CategoryBlogController::class, 'all']);
        Route::get('/{id}',         [CategoryBlogController::class, 'show']);
        Route::post('/',            [CategoryBlogController::class, 'store']);
        Route::post('/update/{id}', [CategoryBlogController::class, 'update']);
        Route::delete('/{id}',      [CategoryBlogController::class, 'destroy']);
    });

    // Deed (admin & notaris)
    Route::prefix('deed')->middleware('ability:notaris,admin')->group(function () {
        Route::get('/',               [DeedController::class, 'index']);
        Route::get('/{id}',           [DeedController::class, 'show']);
        Route::post('/',              [DeedController::class, 'store']);
        Route::post('/update/{id}',   [DeedController::class, 'update']);
        Route::delete('/{id}',        [DeedController::class, 'destroy']);
    });

    // Draft (admin & notaris)
    Route::prefix('draft')->middleware('ability:notaris,admin')->group(function () {
        Route::get('/',                [DraftController::class, 'index']);
        Route::get('/{id}',            [DraftController::class, 'show']);
        Route::post('/',               [DraftController::class, 'store']);
        Route::post('/update/{id}',    [DraftController::class, 'update']);
        Route::delete('/{id}',         [DraftController::class, 'destroy']);
        Route::post('/{id}/render-pdf', [DraftController::class, 'renderPdf']);
    });

    // Requirement master (admin & notaris)
    Route::prefix('requirement')->middleware('ability:admin,notaris')->group(function () {
        Route::get('/',                 [RequirementController::class, 'index']);
        Route::get('/{id}',             [RequirementController::class, 'show']);
        Route::post('/',                [RequirementController::class, 'store']);
        Route::post('/update/{id}',     [RequirementController::class, 'update']);
        Route::delete('/{id}',          [RequirementController::class, 'destroy']);
    });

    // User manajemen (admin)
    Route::prefix('user')->middleware('ability:admin')->group(function () {
        Route::get('/',        [UserController::class, 'getAllUsers']);
        Route::get('/{id}',    [UserController::class, 'getDetailUser']);
        Route::delete('/{id}', [UserController::class, 'destroyUser']);
    });

    // Main Value Deed (admin & notaris)
    Route::prefix('main-value-deed')->middleware('ability:notaris,admin')->group(function () {
        Route::get('/',               [MainValueDeedController::class, 'index']);
        Route::get('/{id}',           [MainValueDeedController::class, 'show']);
        Route::post('/',              [MainValueDeedController::class, 'store']);
        Route::post('/update/{id}',   [MainValueDeedController::class, 'update']);
        Route::delete('/{id}',        [MainValueDeedController::class, 'destroy']);
    });

    // (Opsional) Product CRUD khusus admin — dipindah agar konsisten
    Route::prefix('products')->middleware('ability:admin')->group(function () {
        Route::post('/',               [ProductController::class, 'storeProduct']);
        Route::post('/update/{id}',    [ProductController::class, 'updateProduct']);
        Route::delete('/{id}',         [ProductController::class, 'destroyProduct']);
    });
});

/*
|--------------------------------------------------------------------------
| Notaris Area
|--------------------------------------------------------------------------
*/
Route::prefix('notaris')->middleware(['auth:sanctum'])->group(function () {

    // Upload editor (khusus notaris; pakai checkverif)
    Route::middleware(['ability:notaris', 'checkverif'])->group(function () {
        Route::post('/editor/upload-image', [EditorUploadController::class, 'uploadImage']);
        Route::delete('/editor/image',      [EditorUploadController::class, 'deleteImage']);
    });

    // Activity (CRUD & listing)
    Route::prefix('activity')->middleware('ability:notaris')->group(function () {
        Route::get('/user/approved',           [NotarisActivityController::class, 'getByUserApproved']);
        Route::get('/user/client',             [NotarisActivityController::class, 'getUsers']);
        Route::get('/user/remove/{userid}/{activityid}', [NotarisActivityController::class, 'removeUser']);
        Route::get('/user/add/{userid}/{activityid}',    [NotarisActivityController::class, 'addUser']);

        // status docs -> mark done
        Route::get('/mark-done/docs/{id}',     [NotarisActivityController::class, 'markDone']);

        // create/update/delete butuh verifikasi
        Route::middleware('checkverif')->group(function () {
            Route::post('/',               [NotarisActivityController::class, 'store']);
            Route::post('/update/{id}',    [NotarisActivityController::class, 'update']);
        });
    });
    Route::prefix('activity')->middleware('ability:admin,notaris')->group(function () {
        Route::get('/',                        [NotarisActivityController::class, 'index']);
        Route::delete('/{id}',         [NotarisActivityController::class, 'destroy']);
    });

    // Activity detail bisa diakses notaris & penghadap (verif)
    Route::prefix('activity')
        ->middleware(['ability:admin,notaris,penghadap', 'checkverif'])
        ->group(function () {
            Route::get('/{id}', [NotarisActivityController::class, 'show']);
        });

    // Signing flow (notaris & penghadap; verif)
    Route::prefix('activities/{id}/sign')
        ->middleware(['ability:notaris,penghadap', 'checkverif'])
        ->group(function () {
            Route::post('/apply',      [SignController::class, 'apply']);
            Route::post('/placements', [SignController::class, 'storePlacements']);
            Route::post('/reset-ttd',  [SignController::class, 'resetTtd']);
            Route::post('/done',       [SignController::class, 'markDone']);
        });

    // Track admin (khusus notaris; verif)
    Route::prefix('track')
        ->middleware(['ability:notaris', 'checkverif'])
        ->group(function () {
            Route::get('/{id}',  [TrackController::class, 'show']);
            Route::post('/{id}', [TrackController::class, 'update']);
        });

    // Schedule (notaris manage; penghadap bisa lihat via /schedule/user di shared)
    Route::prefix('schedule')
        ->middleware(['ability:admin,penghadap,notaris', 'checkverif'])
        ->group(function () {
            Route::get('/',               [ScheduleController::class, 'index']);   // notaris list miliknya
            Route::get('/{id}',           [ScheduleController::class, 'show']);
            Route::post('/',              [ScheduleController::class, 'store']);
            Route::post('/update/{id}',   [ScheduleController::class, 'update']);
            Route::delete('/{id}',        [ScheduleController::class, 'destroy']);
        });

    // Document Requirement (notaris perspective)
    Route::prefix('document-requirement')
        ->middleware(['ability:notaris', 'checkverif'])
        ->group(function () {
            Route::get('/by-activity-notaris/{id}',                            [DocumentRequirementController::class, 'getRequirementByActivityNotaris']);
            Route::post('/by-activity-notaris/{idActivity}/{idUser}',          [DocumentRequirementController::class, 'getRequirementByActivityNotarisForUser']);
            Route::post('/approval/{id}',                                      [DocumentRequirementController::class, 'approval']);
            Route::get('/activity/{id}',                                       [DocumentRequirementController::class, 'getByActivity']);
        });
});

/*
|--------------------------------------------------------------------------
| User Area (penghadap & notaris; lalu sub-area khusus penghadap)
|--------------------------------------------------------------------------
*/
Route::prefix('user')->middleware(['auth:sanctum'])->group(function () {

    // Profil & identitas (semua role yang relevan)
    Route::middleware('ability:penghadap,notaris,admin')->group(function () {
        Route::get('/profile',           [UserController::class, 'getProfile']);
        Route::get('/profile/{id}',      [UserController::class, 'getProfileById']);
        Route::post('/update-profile',   [UserController::class, 'updateProfile']);
        Route::post('/update-identity-profile', [UserController::class, 'updateIdentityProfile']);

        // Document requirement dari sisi user
        Route::prefix('document-requirement')->middleware('checkverif')->group(function () {
            Route::get('/by-activity-user/{id}', [DocumentRequirementController::class, 'getRequirementByActivityUser']);
            Route::get('/',                      [DocumentRequirementController::class, 'index']);
            Route::get('/{id}',                  [DocumentRequirementController::class, 'show']);
            Route::post('/',                     [DocumentRequirementController::class, 'store']);
            Route::delete('/{id}',               [DocumentRequirementController::class, 'destroy']);
        });
    });

    // Client approval activity (khusus penghadap)
    Route::middleware('ability:penghadap')->group(function () {
        Route::post('/activity/approval/{id}', [ClientActivityController::class, 'clientApproval'])->middleware('checkverif');

        Route::prefix('activity')->group(function () {
            Route::get('/',     [ClientActivityController::class, 'index']);
            Route::get('/{id}', [ClientActivityController::class, 'show']);
        });
    });

    // Client approval draft (khusus penghadap)
    Route::middleware('ability:penghadap')->group(function () {
        Route::post('/drafts/approval/{id}', [ClientDraftController::class, 'clientApproval'])->middleware('checkverif');

        Route::prefix('drafts')->group(function () {
            Route::get('/',     [ClientDraftController::class, 'index']);
            Route::get('/{id}', [ClientDraftController::class, 'show']);
        });
    });

    // Update document requirement (user/notaris)
    Route::prefix('document-requirement')->middleware('ability:penghadap,notaris')->group(function () {
        Route::post('/update/{id}', [DocumentRequirementController::class, 'update'])->middleware('checkverif');
    });
});
