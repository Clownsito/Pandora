<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CachedProductController;
use App\Http\Controllers\ProductSimulationController;
use App\Http\Controllers\ProductSuggestionController;
use App\Http\Controllers\AdminUserController;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth', 'company'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('/products', [CachedProductController::class, 'index'])->name('products.index');

    Route::post('/products/import', [CachedProductController::class, 'import'])->name('products.import');

    Route::post('/products/import-bot', [CachedProductController::class, 'importBotFile'])->name('products.importBot');

    Route::get('/products/{product}/suggestions', ProductSuggestionController::class)->name('products.suggestions');

    Route::post('/products/{product}/simulate', [ProductSimulationController::class, 'simulate'])->name('products.simulate');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes protected by auth and admin middleware
Route::middleware(['auth', 'admin'])->group(function () {
    Route::match(['get', 'post'], '/admin/users/manage', [AdminUserController::class, 'manage'])->name('admin.users.manage');
    Route::get('/admin/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

require __DIR__.'/auth.php';