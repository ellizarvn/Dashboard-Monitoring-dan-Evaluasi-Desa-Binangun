<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Dashboard Kinerja Desa Binangun
|--------------------------------------------------------------------------
|
| Semua route diorganisasi dalam grup middleware:
| - 'auth'  : Harus login
| - 'role:X': Pembatasan berdasarkan role RBAC
|
*/

// ============================================================
// AUTENTIKASI (Publik - tidak perlu login)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.store');
});

// Redirect root ke dashboard atau login
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('auth.login');
});

// ============================================================
// AREA TERPROTEKSI (Wajib Login)
// ============================================================
Route::middleware(['auth'])->group(function () {

    // ---- Logout ----
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // ---- Pengaturan Akun & Keamanan ----
    Route::prefix('pengaturan')->name('settings.')->group(function () {
        Route::get('/', [AuthController::class, 'showSettings'])->name('index');
        Route::post('/ubah-password', [AuthController::class, 'updatePassword'])->name('password.update');
        Route::post('/toggle-2fa', [AuthController::class, 'toggle2fa'])->name('2fa.toggle');
        Route::delete('/sesi/{sessionId}', [AuthController::class, 'revokeSession'])->name('session.revoke');
    });

    // ---- Dashboard Utama (semua role) ----
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ============================================================
    // OKR 1: Partisipasi & Kegiatan Desa
    // ============================================================
    Route::prefix('okr/partisipasi')->name('okr1.')->group(function () {
        Route::get('/', [DashboardController::class, 'okr1Index'])->name('index');

        // Hanya admin dan tim_monitoring yang bisa input/edit
        Route::middleware('role:admin,tim_monitoring')->group(function () {
            Route::post('/simpan', [DashboardController::class, 'okr1Store'])->name('store');
        });
    });

    // ============================================================
    // OKR 2: Penguatan Ekonomi Lokal (BUMDes)
    // ============================================================
    Route::prefix('okr/ekonomi')->name('okr2.')->group(function () {
        Route::get('/', [DashboardController::class, 'okr2Index'])->name('index');

        Route::middleware('role:admin,tim_monitoring')->group(function () {
            Route::post('/transaksi', [DashboardController::class, 'okr2StoreTransaksi'])->name('transaksi.store');
            Route::post('/pades', [DashboardController::class, 'okr2StorePades'])->name('pades.store');
        });
    });

    // ============================================================
    // OKR 3: Kapasitas SDM
    // ============================================================
    Route::prefix('okr/sdm')->name('okr3.')->group(function () {
        Route::get('/', [DashboardController::class, 'okr3Index'])->name('index');

        Route::middleware('role:admin,tim_monitoring')->group(function () {
            Route::post('/simpan', [DashboardController::class, 'okr3Store'])->name('store');
        });
    });

    // ============================================================
    // TARGET TAHUNAN (hanya admin & kepala_desa bisa edit)
    // ============================================================
    Route::prefix('target-tahunan')->name('target.')->group(function () {
        Route::get('/', [DashboardController::class, 'targetIndex'])->name('index');

        Route::middleware('role:admin,kepala_desa')->group(function () {
            Route::post('/simpan', [DashboardController::class, 'targetStore'])->name('store');
        });
    });

    // ============================================================
    // PROGRAM DESA
    // ============================================================
    Route::prefix('program')->name('programs.')->group(function () {
        Route::get('/', [ProgramController::class, 'index'])->name('index');
        Route::get('/{program}', [ProgramController::class, 'show'])->name('show');

        Route::middleware('role:admin,tim_monitoring')->group(function () {
            Route::post('/', [ProgramController::class, 'store'])->name('store');
            Route::put('/{program}', [ProgramController::class, 'update'])->name('update');
        });

        Route::middleware('role:admin')->group(function () {
            Route::delete('/{program}', [ProgramController::class, 'destroy'])->name('destroy');
        });
    });

    // ============================================================
    // LAPORAN DESA
    // ============================================================
    Route::prefix('laporan')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');

        Route::middleware('role:admin,tim_monitoring,kepala_desa')->group(function () {
            Route::post('/', [ReportController::class, 'store'])->name('store');
        });

        Route::middleware('role:admin,kepala_desa')->group(function () {
            Route::post('/{report}/publish', [ReportController::class, 'publish'])->name('publish');
        });

        Route::middleware('role:admin')->group(function () {
            Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
        });
    });

    // ============================================================
    // AUDIT LOG (admin & kepala_desa)
    // ============================================================
    Route::prefix('audit-log')->name('audit.')->group(function () {
        Route::middleware('role:admin,kepala_desa')->group(function () {
            Route::get('/', [AuditLogController::class, 'index'])->name('index');
        });

        Route::middleware('role:admin')->group(function () {
            Route::get('/export-csv', [AuditLogController::class, 'exportCsv'])->name('export.csv');
        });
    });

    // ============================================================
    // PUSAT BANTUAN (semua role)
    // ============================================================
    Route::get('/bantuan', function () {
        return view('help.index');
    })->name('help.index');

});

// ============================================================
// Fallback Route - 404
// ============================================================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
