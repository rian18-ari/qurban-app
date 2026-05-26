<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/periods', [\App\Http\Controllers\SettingController::class, 'store'])->name('settings.periods.store');
    Route::put('/settings/periods/{period}', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.periods.update');
    Route::post('/settings/periods/{period}/set-active', [\App\Http\Controllers\SettingController::class, 'setActive'])->name('settings.periods.set-active');

    Route::get('/mudhohis', [\App\Http\Controllers\MudhohiController::class, 'index'])->name('mudhohis.index');
    Route::get('/mudhohis/create', [\App\Http\Controllers\MudhohiController::class, 'create'])->name('mudhohis.create');
    Route::post('/mudhohis', [\App\Http\Controllers\MudhohiController::class, 'store'])->name('mudhohis.store');

    Route::get('/sessions', [\App\Http\Controllers\DistributionSessionController::class, 'index'])->name('sessions.index');
    Route::post('/sessions', [\App\Http\Controllers\DistributionSessionController::class, 'store'])->name('sessions.store');

    Route::get('/mustahiqs', [\App\Http\Controllers\MustahiqController::class, 'index'])->name('mustahiqs.index');
    Route::post('/mustahiqs/import', [\App\Http\Controllers\MustahiqController::class, 'import'])->name('mustahiqs.import');

    Route::get('/jagal', [\App\Http\Controllers\JagalController::class, 'index'])->name('jagal.index');
    Route::post('/jagal/animals/{animal}/status', [\App\Http\Controllers\JagalController::class, 'updateStatus'])->name('jagal.update-status');

    Route::get('/gatekeeper', [\App\Http\Controllers\GatekeeperController::class, 'index'])->name('gatekeeper.index');
    Route::post('/gatekeeper/validate', [\App\Http\Controllers\GatekeeperController::class, 'validateCoupon'])->name('gatekeeper.validate');

    Route::get('/financial', [\App\Http\Controllers\FinancialController::class, 'index'])->name('financial.index');
    Route::post('/financial', [\App\Http\Controllers\FinancialController::class, 'store'])->name('financial.store');

    Route::get('/reports/export', [\App\Http\Controllers\ReportController::class, 'exportExcel'])->name('reports.export');
});

require __DIR__.'/auth.php';
