<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CollecteController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

// ✅ Route racine
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        return match ($role) {
            'admin', 'proprietaire' => redirect()->route('dashboard'),
            'gestionnaire' => redirect()->route('collectes.index'),
            'collecteur' => redirect()->route('collectes.index'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// ✅ Routes PUBLIQUES
Route::get('/invitation/{token}', [UserController::class, 'acceptForm'])
    ->name('invitation.accept');
Route::post('/invitation/{token}', [UserController::class, 'acceptStore'])
    ->name('invitation.accept.store');

// ✅ Routes publiques réinitialisation mot de passe
Route::get('/set-password/{token}', [PasswordResetLinkController::class, 'form'])
    ->name('password.reset.form');
Route::post('/set-password/{token}', [PasswordResetLinkController::class, 'store'])
    ->name('password.reset.store');
// ✅ Routes PROTÉGÉES
Route::middleware(['auth', 'role'])->group(function () {

    /* ---- Dashboard ---- */
    Route::middleware('role:admin,proprietaire')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
    });

    /* ---- Points d'eau ---- */
    Route::middleware('role:admin,proprietaire,gestionnaire')->group(function () {
        Route::get('/points', [PointController::class, 'index'])->name('points.index');
        Route::get('/points/create', [PointController::class, 'create'])->name('points.create');
        Route::post('/points', [PointController::class, 'store'])->name('points.store');
        Route::get('/points/{point}', [PointController::class, 'show'])->name('points.show');
        Route::get('/points/{point}/edit', [PointController::class, 'edit'])->name('points.edit');
        Route::put('/points/{point}', [PointController::class, 'update'])->name('points.update');
    });

    Route::middleware('role:admin,proprietaire')->group(function () {
        Route::delete('/points/{point}', [PointController::class, 'destroy'])->name('points.destroy');
    });

    /* ---- Collectes ---- */
    Route::middleware('role:admin,proprietaire,gestionnaire,collecteur')->group(function () {
        Route::get('/collectes', [CollecteController::class, 'index'])->name('collectes.index');
        Route::get('/collectes/create', [CollecteController::class, 'create'])->name('collectes.create');
        Route::post('/collectes', [CollecteController::class, 'store'])->name('collectes.store');
        Route::get('/collectes/{collecte}/edit', [CollecteController::class, 'edit'])->name('collectes.edit');
        Route::put('/collectes/{collecte}', [CollecteController::class, 'update'])->name('collectes.update');
    });

    Route::middleware('role:admin,proprietaire')->group(function () {
        Route::delete('/collectes/{collecte}', [CollecteController::class, 'destroy'])->name('collectes.destroy');
    });

    /* ---- Dépenses ---- */
    Route::middleware('role:admin,proprietaire,gestionnaire')->group(function () {
        Route::get('/depenses', [DepenseController::class, 'index'])->name('depenses.index');
        Route::get('/depenses/create', [DepenseController::class, 'create'])->name('depenses.create');
        Route::post('/depenses', [DepenseController::class, 'store'])->name('depenses.store');
        Route::get('/depenses/{depense}/edit', [DepenseController::class, 'edit'])->name('depenses.edit');
        Route::put('/depenses/{depense}', [DepenseController::class, 'update'])->name('depenses.update');
    });

    Route::middleware('role:admin,proprietaire')->group(function () {
        Route::delete('/depenses/{depense}', [DepenseController::class, 'destroy'])->name('depenses.destroy');
    });

    /* ---- Administration ---- */
    Route::middleware('role:admin,proprietaire')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::post('users/{user}/resend-invitation', [UserController::class, 'resendInvitation'])->name('users.resend');
        Route::post('users/{user}/reset-password', [PasswordResetLinkController::class, 'generate'])->name('users.reset-password');
        Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    });
});

require __DIR__ . '/auth.php';