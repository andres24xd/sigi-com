<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Actividad2Test extends TestCase
{
    use RefreshDatabase;

    public function test_el_seeder_puebla_las_nueve_tablas(): void
    {
        $this->seed();

        $this->assertSame(2, User::count());
        $this->assertSame(4, Categoria::count());
        $this->assertSame(3, Proveedor::count());
        $this->assertSame(3, Cliente::count());
        $this->assertSame(10, Producto::count());
        $this->assertSame(3, Compra::count());
        $this->assertSame(10, DetalleCompra::count());
        $this->assertSame(4, Venta::count());
        $this->assertSame(10, DetalleVenta::count());
    }

    public function test_el_stock_coincide_con_las_compras_menos_las_ventas(): void
    {
        $this->seed();

        foreach (Producto::all() as $producto) {
            $comprado = (int) DetalleCompra::where('producto_id', $producto->id)->sum('cantidad');
            $vendido = (int) DetalleVenta::where('producto_id', $producto->id)->sum('cantidad');

            $this->assertSame(
                $comprado - $vendido,
                $producto->stock_actual,
                "El stock de {$producto->nombre} no coincide con sus movimientos."
            );
        }
    }

    public function test_los_totales_de_las_cabeceras_coinciden_con_sus_detalles(): void
    {
        $this->seed();

        foreach (Compra::all() as $compra) {
            $calculado = $compra->detalles()->sum(DB::raw('cantidad * precio_unitario'));
            $this->assertEquals($compra->total, $calculado);
        }

        foreach (Venta::all() as $venta) {
            $calculado = $venta->detalles()->sum(DB::raw('cantidad * precio_unitario'));
            $this->assertEquals($venta->total, $calculado);
        }
    }

    public function test_las_relaciones_eloquent_funcionan(): void
    {
        $this->seed();

        $producto = Producto::with('categoria')->firstOrFail();
        $this->assertInstanceOf(Categoria::class, $producto->categoria);

        $compra = Compra::with('proveedor', 'detalles.producto')->firstOrFail();
        $this->assertInstanceOf(Proveedor::class, $compra->proveedor);
        $this->assertInstanceOf(Producto::class, $compra->detalles->first()->producto);

        $venta = Venta::with('usuario', 'detalles')->firstOrFail();
        $this->assertInstanceOf(User::class, $venta->usuario);
        $this->assertTrue($venta->usuario->ventas()->whereKey($venta->id)->exists());
    }

    public function test_existe_al_menos_una_venta_a_consumidor_final(): void
    {
        $this->seed();

        $this->assertTrue(
            Venta::whereNull('cliente_id')->exists(),
            'El seeder debe demostrar una venta sin cliente registrado.'
        );
    }

    public function test_las_factories_generan_registros(): void
    {
        $compra = Compra::factory()->create();
        $venta = Venta::factory()->create();
        $producto = Producto::factory()->create();

        $this->assertDatabaseHas('compras', ['id' => $compra->id]);
        $this->assertDatabaseHas('ventas', ['id' => $venta->id]);
        $this->assertDatabaseHas('productos', ['id' => $producto->id]);
        $this->assertNotNull($producto->categoria);
    }
}
