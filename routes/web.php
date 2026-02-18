<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DeduplicationController;
use App\Http\Controllers\DuplicatePairController;
use App\Http\Controllers\MasterlistController;
use App\Http\Controllers\MasterlistExportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Dashboard'))
    ->name('home')
    ->middleware('auth');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::delete('/logout', [LoginController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

Route::middleware('auth')->group(function (): void {
    Route::resource('masterlists', MasterlistController::class)->only(['index', 'store']);
    Route::post('masterlists/{masterlist}/deduplicate', [DeduplicationController::class, 'run'])->name('masterlists.deduplicate');
    Route::get('masterlists/{masterlist}/review', [DuplicatePairController::class, 'index'])->name('masterlists.review');
    Route::patch('duplicate-pairs/{pair}', [DuplicatePairController::class, 'update'])->name('duplicate-pairs.update');
    Route::get('masterlists/{masterlist}/export', MasterlistExportController::class)->name('masterlists.export');
});
