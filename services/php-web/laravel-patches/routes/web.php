<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IssController;
use App\Http\Controllers\OsdrController;

Route::get('/', fn() => view('welcome'));

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
Route::get('/dashboard-minimal', [\App\Http\Controllers\DashboardController::class, 'minimal']);

Route::get('/osdr', [OsdrController::class, 'index']);
Route::get('/osdr-new', [OsdrController::class, 'index'])->name('osdr.new');
Route::get('/api/osdr/proxy-fetch', [OsdrController::class, 'proxyFetch'])->name('osdr.api.proxy.fetch');

Route::prefix('api/osdr')->group(function () {
    Route::get('/list', [OsdrController::class, 'apiList'])->name('osdr.api.list');
    Route::get('/summary', [OsdrController::class, 'apiSummary'])->name('osdr.api.summary');
    Route::post('/refresh', [OsdrController::class, 'apiRefresh'])->name('osdr.api.refresh');
    Route::get('/dataset/{datasetId}', [OsdrController::class, 'apiDataset'])->name('osdr.api.dataset');
});

Route::get('/astronomy', [\App\Http\Controllers\AstroController::class, 'index']);
Route::get('/api/astronomy/events', [\App\Http\Controllers\AstroController::class, 'events']);

Route::get('/cms-admin', [\App\Http\Controllers\CmsController::class, 'admin']);
Route::get('/page/{slug}', [\App\Http\Controllers\CmsController::class, 'page']);
Route::get('/cms/create', [\App\Http\Controllers\CmsController::class, 'create'])->name('cms.create');
Route::get('/cms/edit/{id}', [\App\Http\Controllers\CmsController::class, 'edit'])->name('cms.edit');
Route::post('/cms/store', [\App\Http\Controllers\CmsController::class, 'store'])->name('cms.store');
Route::put('/cms/update/{id}', [\App\Http\Controllers\CmsController::class, 'update'])->name('cms.update');
Route::delete('/cms/delete/{id}', [\App\Http\Controllers\CmsController::class, 'destroy'])->name('cms.destroy');

Route::get('/api/iss/last',  [\App\Http\Controllers\ProxyController::class, 'last']);
Route::get('/api/iss/trend', [\App\Http\Controllers\ProxyController::class, 'trend']);
Route::get('/iss-new', [IssController::class, 'newIndex'])->name('iss.new');
Route::get('/api/iss/history', [IssController::class, 'apiHistory'])->name('iss.api.history');

Route::prefix('api/iss')->group(function () {
    Route::get('/latest', [IssController::class, 'apiLatest'])->name('iss.api.latest');
    Route::get('/trend', [IssController::class, 'apiTrend'])->name('iss.api.trend');
    Route::post('/trigger-fetch', [IssController::class, 'apiTriggerFetch'])->name('iss.api.trigger-fetch');
});

Route::get('/api/jwst/feed', [\App\Http\Controllers\DashboardController::class, 'jwstFeed']);
Route::get("/api/astro/events", [\App\Http\Controllers\AstroController::class, "events"]);

Route::get('/telemetry', [\App\Http\Controllers\TelemetryController::class, 'index'])->name('telemetry');
Route::get('/api/telemetry/data', [\App\Http\Controllers\TelemetryController::class, 'apiData'])->name('telemetry.api.data');
Route::get('/api/telemetry/stats', [\App\Http\Controllers\TelemetryController::class, 'apiStats'])->name('telemetry.api.stats');
Route::get('/api/telemetry/realtime', [\App\Http\Controllers\TelemetryController::class, 'apiRealtime'])->name('telemetry.api.realtime');