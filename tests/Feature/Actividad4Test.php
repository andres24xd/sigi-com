<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Actividad4Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->actingAs(User::where('rol', 'Administrador')->firstOrFail());
    }

    public function test_las_pantallas_de_detalle_de_las_entidades_principales_responden(): void
    {
        $categoria = Categoria::firstOrFail();
        $producto = Producto::firstOrFail();
        $proveedor = Proveedor::firstOrFail();
        $cliente = Cliente::firstOrFail();

        $this->get(route('inventario.categorias.show', $categoria))
            ->assertOk()
            ->assertSee($categoria->nombre);

        $this->get(route('inventario.productos.show', $producto))
            ->assertOk()
            ->assertSee($producto->nombre);

        $this->get(route('terceros.proveedores.show', $proveedor))
            ->assertOk()
            ->assertSee($proveedor->nombre);

        $this->get(route('terceros.clientes.show', $cliente))
            ->assertOk()
            ->assertSee($cliente->nombre);
    }

    public function test_las_rutas_de_detalle_estan_nombradas(): void
    {
        $this->assertTrue(route('inventario.categorias.show', 1, false) !== '');
        $this->assertTrue(route('inventario.productos.show', 1, false) !== '');
        $this->assertTrue(route('terceros.proveedores.show', 1, false) !== '');
        $this->assertTrue(route('terceros.clientes.show', 1, false) !== '');
    }

    public function test_se_puede_actualizar_una_categoria(): void
    {
        $categoria = Categoria::create(['nombre' => 'Categoría original']);

        $this->put(route('inventario.categorias.update', $categoria), ['nombre' => 'Categoría actualizada'])
            ->assertRedirect(route('inventario.categorias.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categorias', [
            'id' => $categoria->id,
            'nombre' => 'Categoría actualizada',
        ]);
    }

    public function test_se_puede_actualizar_un_producto(): void
    {
        $producto = Producto::factory()->create();
        $nuevaCategoria = Categoria::factory()->create();

        $this->put(route('inventario.productos.update', $producto), [
            'nombre' => 'Producto actualizado',
            'categoria_id' => $nuevaCategoria->id,
            'precio_venta' => 25900,
            'stock_actual' => 7,
        ])->assertRedirect(route('inventario.productos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'Producto actualizado',
            'categoria_id' => $nuevaCategoria->id,
            'precio_venta' => 25900,
            'stock_actual' => 7,
        ]);
    }

    public function test_se_puede_actualizar_un_proveedor(): void
    {
        $proveedor = Proveedor::factory()->create();

        $this->put(route('terceros.proveedores.update', $proveedor), [
            'nombre' => 'Proveedor actualizado',
            'telefono' => '300 123 4567',
        ])->assertRedirect(route('terceros.proveedores.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('proveedores', [
            'id' => $proveedor->id,
            'nombre' => 'Proveedor actualizado',
            'telefono' => '300 123 4567',
        ]);
    }

    public function test_se_puede_actualizar_un_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $this->put(route('terceros.clientes.update', $cliente), [
            'nombre' => 'Cliente actualizado',
            'telefono' => '310 111 2233',
        ])->assertRedirect(route('terceros.clientes.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nombre' => 'Cliente actualizado',
            'telefono' => '310 111 2233',
        ]);
    }

    public function test_se_puede_crear_una_categoria_con_mensaje_de_confirmacion(): void
    {
        $this->post(route('inventario.categorias.store'), ['nombre' => 'Categoría de prueba'])
            ->assertRedirect(route('inventario.categorias.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categorias', ['nombre' => 'Categoría de prueba']);
    }

    public function test_se_puede_eliminar_una_categoria_sin_productos(): void
    {
        $categoria = Categoria::factory()->create();

        $this->delete(route('inventario.categorias.destroy', $categoria))
            ->assertRedirect(route('inventario.categorias.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }

    public function test_se_puede_eliminar_un_producto_sin_movimientos(): void
    {
        $producto = Producto::factory()->create();

        $this->delete(route('inventario.productos.destroy', $producto))
            ->assertRedirect(route('inventario.productos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
    }

    public function test_se_puede_eliminar_un_proveedor_sin_compras(): void
    {
        $proveedor = Proveedor::factory()->create();

        $this->delete(route('terceros.proveedores.destroy', $proveedor))
            ->assertRedirect(route('terceros.proveedores.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('proveedores', ['id' => $proveedor->id]);
    }

    public function test_eliminar_un_cliente_conserva_sus_ventas_como_consumidor_final(): void
    {
        $cliente = Cliente::has('ventas')->firstOrFail();
        $venta = Venta::where('cliente_id', $cliente->id)->firstOrFail();

        $this->delete(route('terceros.clientes.destroy', $cliente))
            ->assertRedirect(route('terceros.clientes.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
        $this->assertDatabaseHas('ventas', ['id' => $venta->id, 'cliente_id' => null]);
    }

    public function test_no_se_puede_eliminar_una_categoria_con_productos(): void
    {
        $categoria = Categoria::has('productos')->firstOrFail();

        $this->from(route('inventario.categorias.show', $categoria))
            ->delete(route('inventario.categorias.destroy', $categoria))
            ->assertRedirect(route('inventario.categorias.show', $categoria))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categorias', ['id' => $categoria->id]);
    }

    public function test_no_se_puede_eliminar_un_producto_con_movimientos(): void
    {
        $producto = Producto::has('detallesCompra')->firstOrFail();

        $this->from(route('inventario.productos.show', $producto))
            ->delete(route('inventario.productos.destroy', $producto))
            ->assertRedirect(route('inventario.productos.show', $producto))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('productos', ['id' => $producto->id]);
    }

    public function test_no_se_puede_eliminar_un_proveedor_con_compras(): void
    {
        $proveedor = Proveedor::has('compras')->firstOrFail();

        $this->from(route('terceros.proveedores.show', $proveedor))
            ->delete(route('terceros.proveedores.destroy', $proveedor))
            ->assertRedirect(route('terceros.proveedores.show', $proveedor))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('proveedores', ['id' => $proveedor->id]);
    }

    public function test_la_validacion_rechaza_productos_incompletos_y_conserva_los_datos_previos(): void
    {
        $this->from(route('inventario.productos.create'))
            ->post(route('inventario.productos.store'), [
                'nombre' => 'Producto sin categoría',
                'categoria_id' => '',
                'precio_venta' => -10,
                'stock_actual' => 'mucho',
            ])
            ->assertRedirect(route('inventario.productos.create'))
            ->assertSessionHasErrors(['categoria_id', 'precio_venta', 'stock_actual'])
            ->assertSessionHasInput('nombre', 'Producto sin categoría');

        $this->assertDatabaseMissing('productos', ['nombre' => 'Producto sin categoría']);
    }

    public function test_la_validacion_rechaza_una_categoria_inexistente(): void
    {
        $this->post(route('inventario.productos.store'), [
            'nombre' => 'Producto huérfano',
            'categoria_id' => 999999,
            'precio_venta' => 1000,
            'stock_actual' => 1,
        ])->assertSessionHasErrors('categoria_id');

        $this->assertDatabaseMissing('productos', ['nombre' => 'Producto huérfano']);
    }

    public function test_la_validacion_rechaza_nombres_repetidos_y_telefonos_invalidos(): void
    {
        $categoria = Categoria::firstOrFail();

        $this->post(route('inventario.categorias.store'), ['nombre' => $categoria->nombre])
            ->assertSessionHasErrors('nombre');

        $this->post(route('terceros.clientes.store'), [
            'nombre' => 'Cliente con teléfono inválido',
            'telefono' => 'abc',
        ])->assertSessionHasErrors('telefono');

        $this->assertDatabaseMissing('clientes', ['nombre' => 'Cliente con teléfono inválido']);
    }

    public function test_los_formularios_de_edicion_cargan_los_datos_del_registro(): void
    {
        $producto = Producto::factory()->create();
        $cliente = Cliente::factory()->create();

        $this->get(route('inventario.productos.edit', $producto))
            ->assertOk()
            ->assertSee($producto->nombre);

        $this->get(route('terceros.clientes.edit', $cliente))
            ->assertOk()
            ->assertSee($cliente->nombre);
    }
}
