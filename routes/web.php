<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ClienteController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');



Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::resource('productos', ProductoController::class);

    // Rutas adicionales para funcionalidades específicas
    Route::post('/productos/bulk-update-prices', [ProductoController::class, 'bulkUpdatePrices'])->name('productos.bulk-update-prices');
    Route::patch('/productos/{producto}/restaurar', [ProductoController::class, 'restaurar'])
        ->name('productos.restaurar');

    // Rutas para la gestión de pedidos
    Route::resource('pedidos', PedidoController::class);

    // Rutas adicionales para funcionalidades específicas
    Route::patch('/pedidos/{pedido}/cancelar', [PedidoController::class, 'cancelar'])
        ->name('pedidos.cancelar');
    Route::patch('/pedidos/{pedido}/comprar', [PedidoController::class, 'comprar'])
        ->name('pedidos.comprar');
    Route::patch('/pedidos/{pedido}/restaurar', [PedidoController::class, 'restaurar'])
        ->name('pedidos.restaurar');

    // Rutas para la gestión de clientes
    Route::resource('clientes', ClienteController::class);
    Route::patch('clientes/{cliente}/activar', [ClienteController::class, 'activar'])
        ->name('clientes.activar');
    Route::patch('clientes/{cliente}/desactivar', [ClienteController::class, 'desactivar'])
        ->name('clientes.desactivar');
});


require __DIR__ . '/auth.php';
