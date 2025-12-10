<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

// Панели
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
Route::get('/dashboard-minimal', [\App\Http\Controllers\DashboardController::class, 'minimal']);
Route::get('/osdr', [\App\Http\Controllers\OsdrController::class, 'index']);
Route::get('/osdr-new', [\App\Http\Controllers\OsdrController::class, 'newIndex'])->name('osdr.new');
Route::get('/iss-new', [\App\Http\Controllers\IssController::class, 'newIndex']);

Route::get('/astronomy', [\App\Http\Controllers\AstroController::class, 'index']);
Route::get('/api/astronomy/events', [\App\Http\Controllers\AstroController::class, 'events']);

Route::get('/cms-admin', [\App\Http\Controllers\CmsController::class, 'admin']);

// Прокси к rust_iss
Route::get('/api/iss/last',  [\App\Http\Controllers\ProxyController::class, 'last']);
Route::get('/api/iss/trend', [\App\Http\Controllers\ProxyController::class, 'trend']);

// JWST галерея (JSON)
Route::get('/api/jwst/feed', [\App\Http\Controllers\DashboardController::class, 'jwstFeed']);
Route::get("/api/astro/events", [\App\Http\Controllers\AstroController::class, "events"]);

Route::get('/page/{slug}', [\App\Http\Controllers\CmsController::class, 'page']);
