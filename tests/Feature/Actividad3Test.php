<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Actividad3Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->actingAs(User::where('rol', 'Administrador')->firstOrFail());
    }

    public function test_las_rutas_principales_responden(): void
    {
        $rutas = [
            route('dashboard'),
            route('inventario.categorias.index'),
            route('inventario.categorias.create'),
            route('inventario.productos.index'),
            route('inventario.productos.create'),
            route('terceros.proveedores.index'),
            route('terceros.proveedores.create'),
            route('terceros.clientes.index'),
            route('terceros.clientes.create'),
            route('operaciones.compras.index'),
            route('operaciones.compras.create'),
            route('operaciones.ventas.index'),
            route('operaciones.ventas.create'),
            route('reportes.index'),
            route('reportes.ventas'),
            route('reportes.compras'),
            route('reportes.inventario'),
        ];

        foreach ($rutas as $ruta) {
            $this->get($ruta)->assertOk();
        }
    }

    public function test_las_rutas_estan_nombradas_y_usan_los_verbos_correctos(): void
    {
        $this->assertTrue(Route::has('inventario.productos.index'));
        $this->assertTrue(Route::has('operaciones.ventas.store'));
        $this->assertTrue(Route::has('reportes.inventario'));

        $this->assertContains('POST', Route::getRoutes()->getByName('inventario.productos.store')->methods());
        $this->assertContains('PUT', Route::getRoutes()->getByName('inventario.productos.update')->methods());
        $this->assertContains('DELETE', Route::getRoutes()->getByName('inventario.productos.destroy')->methods());
    }

    public function test_se_puede_crear_una_categoria(): void
    {
        $this->post(route('inventario.categorias.store'), ['nombre' => 'Papelería'])
            ->assertRedirect(route('inventario.categorias.index'));

        $this->assertDatabaseHas('categorias', ['nombre' => 'Papelería']);
    }

    public function test_no_se_permite_repetir_el_nombre_de_una_categoria(): void
    {
        $categoria = Categoria::firstOrFail();

        $this->post(route('inventario.categorias.store'), ['nombre' => $categoria->nombre])
            ->assertSessionHasErrors('nombre');
    }

    public function test_se_puede_crear_un_producto(): void
    {
        $categoria = Categoria::firstOrFail();

        $this->post(route('inventario.productos.store'), [
            'nombre' => 'Producto de prueba',
            'categoria_id' => $categoria->id,
            'precio_venta' => 4500,
            'stock_actual' => 8,
        ])->assertRedirect(route('inventario.productos.index'));

        $this->assertDatabaseHas('productos', ['nombre' => 'Producto de prueba', 'stock_actual' => 8]);
    }

    public function test_una_compra_aumenta_el_stock_y_calcula_el_total(): void
    {
        $producto = Producto::firstOrFail();
        $proveedor = Proveedor::firstOrFail();
        $stockInicial = $producto->stock_actual;

        $this->post(route('operaciones.compras.store'), [
            'proveedor_id' => $proveedor->id,
            'fecha' => now()->toDateString(),
            'detalles' => [
                ['producto_id' => $producto->id, 'cantidad' => 5, 'precio_unitario' => 1000],
            ],
        ])->assertRedirect();

        $this->assertSame($stockInicial + 5, $producto->fresh()->stock_actual);
        $this->assertDatabaseHas('compras', ['total' => 5000]);
    }

    public function test_una_venta_descuenta_el_stock_y_usa_el_precio_del_catalogo(): void
    {
        $producto = Producto::firstOrFail();
        $stockInicial = $producto->stock_actual;

        $this->post(route('operaciones.ventas.store'), [
            'cliente_id' => null,
            'fecha' => now()->format('Y-m-d\TH:i'),
            'detalles' => [
                ['producto_id' => $producto->id, 'cantidad' => 2],
            ],
        ])->assertRedirect();

        $producto->refresh();

        $this->assertSame($stockInicial - 2, $producto->stock_actual);

        $venta = Venta::latest('id')->firstOrFail();
        $this->assertEquals(2 * (float) $producto->precio_venta, (float) $venta->total);
    }

    public function test_no_se_puede_vender_mas_stock_del_disponible(): void
    {
        $producto = Producto::firstOrFail();
        $stockInicial = $producto->stock_actual;
        $ventasIniciales = Venta::count();

        $this->from(route('operaciones.ventas.create'))
            ->post(route('operaciones.ventas.store'), [
                'cliente_id' => null,
                'fecha' => now()->format('Y-m-d\TH:i'),
                'detalles' => [
                    ['producto_id' => $producto->id, 'cantidad' => $stockInicial + 1],
                ],
            ])
            ->assertRedirect(route('operaciones.ventas.create'))
            ->assertSessionHasErrors('detalles');

        $this->assertSame($stockInicial, $producto->fresh()->stock_actual);
        $this->assertSame($ventasIniciales, Venta::count());
    }

    public function test_eliminar_una_compra_revierte_el_stock(): void
    {
        $producto = Producto::firstOrFail();
        $proveedor = Proveedor::firstOrFail();

        $this->post(route('operaciones.compras.store'), [
            'proveedor_id' => $proveedor->id,
            'fecha' => now()->toDateString(),
            'detalles' => [
                ['producto_id' => $producto->id, 'cantidad' => 4, 'precio_unitario' => 1200],
            ],
        ]);

        $compra = Compra::latest('id')->firstOrFail();
        $stockConCompra = $producto->fresh()->stock_actual;

        $this->delete(route('operaciones.compras.destroy', $compra))
            ->assertRedirect(route('operaciones.compras.index'));

        $this->assertSame($stockConCompra - 4, $producto->fresh()->stock_actual);
        $this->assertDatabaseMissing('compras', ['id' => $compra->id]);
    }

    public function test_los_reportes_aceptan_filtros_de_fecha(): void
    {
        $this->get(route('reportes.ventas', [
            'desde' => now()->subMonth()->toDateString(),
            'hasta' => now()->toDateString(),
        ]))->assertOk()->assertSee('Reporte de ventas');

        $this->get(route('reportes.compras', [
            'desde' => now()->subMonth()->toDateString(),
            'hasta' => now()->toDateString(),
        ]))->assertOk()->assertSee('Reporte de compras');
    }
}
