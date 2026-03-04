<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');



Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::resource('productos', ProductoController::class);
    Route::post('/productos/bulk-update-prices', [ProductoController::class, 'bulkUpdatePrices'])->name('productos.bulk-update-prices');
    Route::patch('/productos/{producto}/restaurar', [ProductoController::class, 'restaurar'])
    ->name('productos.restaurar');
});


require __DIR__.'/auth.php';
