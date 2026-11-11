<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Panel principal
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Inventario: categorías y productos
|--------------------------------------------------------------------------
*/
Route::prefix('inventario')->name('inventario.')->group(function () {
    Route::resource('categorias', CategoriaController::class)
        ->parameters(['categorias' => 'categoria']);

    Route::resource('productos', ProductoController::class)
        ->parameters(['productos' => 'producto']);
});

/*
|--------------------------------------------------------------------------
| Operaciones: compras y ventas
|--------------------------------------------------------------------------
*/
Route::prefix('operaciones')->name('operaciones.')->group(function () {
    Route::resource('compras', CompraController::class)
        ->parameters(['compras' => 'compra'])
        ->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::resource('ventas', VentaController::class)
        ->parameters(['ventas' => 'venta'])
        ->only(['index', 'create', 'store', 'show', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Terceros: proveedores y clientes
|--------------------------------------------------------------------------
*/
Route::prefix('terceros')->name('terceros.')->group(function () {
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor']);

    Route::resource('clientes', ClienteController::class)
        ->parameters(['clientes' => 'cliente']);
});

/*
|--------------------------------------------------------------------------
| Reportes
|--------------------------------------------------------------------------
*/
Route::prefix('reportes')->name('reportes.')->controller(ReporteController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/ventas', 'ventas')->name('ventas');
    Route::get('/compras', 'compras')->name('compras');
    Route::get('/inventario', 'inventario')->name('inventario');
});
