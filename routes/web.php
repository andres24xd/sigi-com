<?php

use App\Http\Controllers\AuthController;
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
| Autenticación
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas protegidas
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Inventario: categorías y productos
    |--------------------------------------------------------------------------
    | El cajero puede consultar; solo el administrador crea, edita y elimina.
    */
    Route::prefix('inventario')->name('inventario.')->group(function () {
        // Escritura solo para administradores. Se registra antes de `show`
        // para que /create no sea capturado por /{parametro}.
        Route::middleware('role:Administrador')->group(function () {
            Route::resource('categorias', CategoriaController::class)
                ->parameters(['categorias' => 'categoria'])
                ->except(['index', 'show']);

            Route::resource('productos', ProductoController::class)
                ->parameters(['productos' => 'producto'])
                ->except(['index', 'show']);
        });

        // Lectura para todos los usuarios autenticados.
        Route::resource('categorias', CategoriaController::class)
            ->parameters(['categorias' => 'categoria'])
            ->only(['index', 'show']);

        Route::resource('productos', ProductoController::class)
            ->parameters(['productos' => 'producto'])
            ->only(['index', 'show']);
    });

    /*
    |--------------------------------------------------------------------------
    | Operaciones: compras y ventas
    |--------------------------------------------------------------------------
    */
    Route::prefix('operaciones')->name('operaciones.')->group(function () {
        Route::middleware('role:Administrador')->group(function () {
            Route::resource('compras', CompraController::class)
                ->parameters(['compras' => 'compra'])
                ->only(['index', 'create', 'store', 'show', 'destroy']);
        });

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
        Route::middleware('role:Administrador')->group(function () {
            Route::resource('proveedores', ProveedorController::class)
                ->parameters(['proveedores' => 'proveedor']);
        });

        Route::resource('clientes', ClienteController::class)
            ->parameters(['clientes' => 'cliente']);
    });

    /*
    |--------------------------------------------------------------------------
    | Reportes
    |--------------------------------------------------------------------------
    | El cajero solo consulta sus propias ventas.
    */
    Route::prefix('reportes')->name('reportes.')->controller(ReporteController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/ventas', 'ventas')->name('ventas');

        Route::middleware('role:Administrador')->group(function () {
            Route::get('/compras', 'compras')->name('compras');
            Route::get('/inventario', 'inventario')->name('inventario');
        });
    });
});
