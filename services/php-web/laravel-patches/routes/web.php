<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RateLimitMiddleware;

Route::get('/', fn() => redirect('/dashboard'));

// Применяем rate limit к API
Route::middleware([RateLimitMiddleware::class.':60,1'])->group(function () {
    // Прокси к rust_iss
    Route::get('/api/iss/last',  [\App\Http\Controllers\ProxyController::class, 'last']);
    Route::get('/api/iss/trend', [\App\Http\Controllers\ProxyController::class, 'trend']);
    
    // JWST галерея (JSON)
    Route::get('/api/jwst/feed', [\App\Http\Controllers\DashboardController::class, 'jwstFeed']);
    Route::get("/api/astro/events", [\App\Http\Controllers\AstroController::class, "events"]);
});

// Панели - разделены по контекстам
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
Route::get('/osdr',      [\App\Http\Controllers\OsdrController::class, 'index'])->name('osdr');
Route::get('/telemetry', [\App\Http\Controllers\TelemetryController::class, 'index'])->name('telemetry');
Route::get('/telemetry/csv', [\App\Http\Controllers\TelemetryController::class, 'csvList'])->name('telemetry.csv.list');
Route::get('/telemetry/csv/{filename}', [\App\Http\Controllers\TelemetryController::class, 'csvView'])->name('telemetry.csv.view');

// CMS
Route::get('/page/{slug}', [\App\Http\Controllers\CmsController::class, 'page'])->name('cms.page');
