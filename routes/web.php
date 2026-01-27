<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CachedProductController;
use App\Http\Controllers\ProductSimulationController;
use App\Http\Controllers\ProductApprovalController;
use App\Http\Controllers\ProductSuggestionController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'));

/*
|--------------------------------------------------------------------------
| Auth + Company
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'company'])->group(function () {

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('/products', [CachedProductController::class, 'index'])
        ->name('products.index');

    Route::post('/products/{product}/simulate', [ProductSimulationController::class, 'simulate']);
    Route::post('/products/{product}/approve', [ProductApprovalController::class, 'store']);

    // 👉 SUGERENCIAS
    Route::get(
        '/products/{product}/suggestions',
        ProductSuggestionController::class
    )->name('products.suggestions');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
