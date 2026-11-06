<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Puebla la base de datos con información de prueba coherente.
     *
     * El stock de cada producto se calcula a partir de los movimientos:
     * las compras lo aumentan y las ventas lo disminuyen (RF09).
     */
    public function run(): void
    {
        try {
            // Deshabilitar FKs es necesario para truncar tablas relacionadas.
            Schema::disableForeignKeyConstraints();

            // Los TRUNCATE son DDL y hacen commit implícito, por eso van
            // antes de abrir la transacción de los datos.
            $this->limpiarTablas();

            DB::beginTransaction();

            $this->cargarDatos();

            DB::commit();

            $this->command?->info('Datos de prueba cargados correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->command?->error('Error al ejecutar el seeder: '.$e->getMessage());

            // Relanzar para que artisan termine con código de error.
            throw $e;
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Vacía las tablas del dominio respetando el orden de dependencias.
     */
    private function limpiarTablas(): void
    {
        foreach ([
            'detalles_ventas',
            'detalles_compras',
            'ventas',
            'compras',
            'productos',
            'clientes',
            'proveedores',
            'categorias',
            'usuarios',
        ] as $tabla) {
            DB::table($tabla)->truncate();
        }
    }

    /**
     * Inserta los datos maestros y los movimientos de compra/venta.
     */
    private function cargarDatos(): void
    {
        $ahora = now();

        // -----------------------------------------------------------------
        // 1. Usuarios
        // -----------------------------------------------------------------
        $usuarios = [
            [
                'nombre' => 'Administrador General',
                'email' => 'admin@sigicom.com',
                'rol' => 'Administrador',
                'password' => Hash::make('admin123'),
            ],
            [
                'nombre' => 'Cajero Mostrador',
                'email' => 'cajero@sigicom.com',
                'rol' => 'Cajero',
                'password' => Hash::make('cajero123'),
            ],
        ];

        $idsUsuarios = [];

        foreach ($usuarios as $usuario) {
            $idsUsuarios[$usuario['email']] = DB::table('usuarios')->insertGetId(
                array_merge($usuario, ['created_at' => $ahora, 'updated_at' => $ahora])
            );
        }

        // -----------------------------------------------------------------
        // 2. Categorías
        // -----------------------------------------------------------------
        $idsCategorias = [];

        foreach (['Abarrotes', 'Bebidas', 'Aseo', 'Lácteos'] as $nombre) {
            $idsCategorias[$nombre] = DB::table('categorias')->insertGetId([
                'nombre' => $nombre,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        // -----------------------------------------------------------------
        // 3. Proveedores
        // -----------------------------------------------------------------
        $proveedores = [
            ['nombre' => 'Distribuidora Central S.A.S.', 'telefono' => '3001234567'],
            ['nombre' => 'Lácteos del Caribe', 'telefono' => '3012223344'],
            ['nombre' => 'Aseo Total S.A.S.', 'telefono' => '3023334455'],
        ];

        $idsProveedores = [];

        foreach ($proveedores as $proveedor) {
            $idsProveedores[$proveedor['nombre']] = DB::table('proveedores')->insertGetId(
                array_merge($proveedor, ['created_at' => $ahora, 'updated_at' => $ahora])
            );
        }

        // -----------------------------------------------------------------
        // 4. Clientes
        // -----------------------------------------------------------------
        $clientes = [
            ['nombre' => 'María Pérez', 'telefono' => '3101112233'],
            ['nombre' => 'Jorge Díaz', 'telefono' => '3112223344'],
            ['nombre' => 'Ana Torres', 'telefono' => '3123334455'],
        ];

        $idsClientes = [];

        foreach ($clientes as $cliente) {
            $idsClientes[$cliente['nombre']] = DB::table('clientes')->insertGetId(
                array_merge($cliente, ['created_at' => $ahora, 'updated_at' => $ahora])
            );
        }

        // -----------------------------------------------------------------
        // 5. Productos (el stock arranca en 0 y lo mueven los detalles)
        // -----------------------------------------------------------------
        $productos = [
            ['nombre' => 'Arroz Diana 1 kg', 'categoria' => 'Abarrotes', 'precio_venta' => 3500],
            ['nombre' => 'Aceite Premier 1 L', 'categoria' => 'Abarrotes', 'precio_venta' => 12500],
            ['nombre' => 'Panela 500 g', 'categoria' => 'Abarrotes', 'precio_venta' => 2500],
            ['nombre' => 'Gaseosa Coca-Cola 1.5 L', 'categoria' => 'Bebidas', 'precio_venta' => 6500],
            ['nombre' => 'Agua Cristal 600 ml', 'categoria' => 'Bebidas', 'precio_venta' => 2000],
            ['nombre' => 'Jugo Hit 500 ml', 'categoria' => 'Bebidas', 'precio_venta' => 2800],
            ['nombre' => 'Jabón Rey 300 g', 'categoria' => 'Aseo', 'precio_venta' => 3000],
            ['nombre' => 'Detergente Fab 1 kg', 'categoria' => 'Aseo', 'precio_venta' => 9000],
            ['nombre' => 'Leche Alquería 1 L', 'categoria' => 'Lácteos', 'precio_venta' => 4200],
            ['nombre' => 'Queso Campesino 250 g', 'categoria' => 'Lácteos', 'precio_venta' => 7000],
        ];

        $idsProductos = [];

        foreach ($productos as $producto) {
            $idsProductos[$producto['nombre']] = DB::table('productos')->insertGetId([
                'nombre' => $producto['nombre'],
                'categoria_id' => $idsCategorias[$producto['categoria']],
                'precio_venta' => $producto['precio_venta'],
                'stock_actual' => 0,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        // -----------------------------------------------------------------
        // 6. Compras + detalles (suman stock)
        // -----------------------------------------------------------------
        $compras = [
            [
                'proveedor' => 'Distribuidora Central S.A.S.',
                'fecha' => $ahora->copy()->subDays(4)->setTime(8, 0),
                'detalles' => [
                    ['producto' => 'Arroz Diana 1 kg', 'cantidad' => 50, 'precio_unitario' => 2800],
                    ['producto' => 'Aceite Premier 1 L', 'cantidad' => 30, 'precio_unitario' => 10000],
                    ['producto' => 'Gaseosa Coca-Cola 1.5 L', 'cantidad' => 40, 'precio_unitario' => 5000],
                    ['producto' => 'Panela 500 g', 'cantidad' => 2, 'precio_unitario' => 1800],
                    ['producto' => 'Jugo Hit 500 ml', 'cantidad' => 30, 'precio_unitario' => 2000],
                    ['producto' => 'Agua Cristal 600 ml', 'cantidad' => 48, 'precio_unitario' => 1500],
                ],
            ],
            [
                'proveedor' => 'Lácteos del Caribe',
                'fecha' => $ahora->copy()->subDays(3)->setTime(8, 30),
                'detalles' => [
                    ['producto' => 'Leche Alquería 1 L', 'cantidad' => 60, 'precio_unitario' => 3500],
                    ['producto' => 'Queso Campesino 250 g', 'cantidad' => 8, 'precio_unitario' => 5500],
                ],
            ],
            [
                'proveedor' => 'Aseo Total S.A.S.',
                'fecha' => $ahora->copy()->subDays(2)->setTime(9, 0),
                'detalles' => [
                    ['producto' => 'Jabón Rey 300 g', 'cantidad' => 35, 'precio_unitario' => 2200],
                    ['producto' => 'Detergente Fab 1 kg', 'cantidad' => 25, 'precio_unitario' => 7000],
                ],
            ],
        ];

        foreach ($compras as $compra) {
            $compraId = DB::table('compras')->insertGetId([
                'proveedor_id' => $idsProveedores[$compra['proveedor']],
                'fecha' => $compra['fecha']->toDateString(),
                'total' => 0,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            $total = 0;

            foreach ($compra['detalles'] as $detalle) {
                $total += $detalle['cantidad'] * $detalle['precio_unitario'];

                DB::table('detalles_compras')->insert([
                    'compra_id' => $compraId,
                    'producto_id' => $idsProductos[$detalle['producto']],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);

                DB::table('productos')
                    ->where('id', $idsProductos[$detalle['producto']])
                    ->increment('stock_actual', $detalle['cantidad'], ['updated_at' => $ahora]);
            }

            DB::table('compras')->where('id', $compraId)->update(['total' => $total]);
        }

        // -----------------------------------------------------------------
        // 7. Ventas + detalles (restan stock)
        // -----------------------------------------------------------------
        $ventas = [
            [
                'cliente' => 'María Pérez',
                'usuario' => 'cajero@sigicom.com',
                'fecha' => $ahora->copy()->subDays(3)->setTime(10, 15),
                'detalles' => [
                    ['producto' => 'Arroz Diana 1 kg', 'cantidad' => 3, 'precio_unitario' => 3500],
                    ['producto' => 'Leche Alquería 1 L', 'cantidad' => 2, 'precio_unitario' => 4200],
                ],
            ],
            [
                'cliente' => null, // Consumidor final
                'usuario' => 'cajero@sigicom.com',
                'fecha' => $ahora->copy()->subDays(2)->setTime(16, 40),
                'detalles' => [
                    ['producto' => 'Gaseosa Coca-Cola 1.5 L', 'cantidad' => 2, 'precio_unitario' => 6500],
                    ['producto' => 'Jabón Rey 300 g', 'cantidad' => 1, 'precio_unitario' => 3000],
                ],
            ],
            [
                'cliente' => 'Jorge Díaz',
                'usuario' => 'admin@sigicom.com',
                'fecha' => $ahora->copy()->subDay()->setTime(11, 20),
                'detalles' => [
                    ['producto' => 'Aceite Premier 1 L', 'cantidad' => 1, 'precio_unitario' => 12500],
                    ['producto' => 'Detergente Fab 1 kg', 'cantidad' => 1, 'precio_unitario' => 9000],
                    ['producto' => 'Queso Campesino 250 g', 'cantidad' => 1, 'precio_unitario' => 7000],
                ],
            ],
            [
                'cliente' => 'Ana Torres',
                'usuario' => 'cajero@sigicom.com',
                'fecha' => $ahora->copy()->setTime(9, 5),
                'detalles' => [
                    ['producto' => 'Agua Cristal 600 ml', 'cantidad' => 3, 'precio_unitario' => 2000],
                    ['producto' => 'Jugo Hit 500 ml', 'cantidad' => 2, 'precio_unitario' => 2800],
                    ['producto' => 'Panela 500 g', 'cantidad' => 2, 'precio_unitario' => 2500],
                ],
            ],
        ];

        foreach ($ventas as $venta) {
            $ventaId = DB::table('ventas')->insertGetId([
                'cliente_id' => $venta['cliente'] !== null ? $idsClientes[$venta['cliente']] : null,
                'usuario_id' => $idsUsuarios[$venta['usuario']],
                'fecha' => $venta['fecha']->toDateTimeString(),
                'total' => 0,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            $total = 0;

            foreach ($venta['detalles'] as $detalle) {
                $total += $detalle['cantidad'] * $detalle['precio_unitario'];

                DB::table('detalles_ventas')->insert([
                    'venta_id' => $ventaId,
                    'producto_id' => $idsProductos[$detalle['producto']],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);

                DB::table('productos')
                    ->where('id', $idsProductos[$detalle['producto']])
                    ->decrement('stock_actual', $detalle['cantidad'], ['updated_at' => $ahora]);
            }

            DB::table('ventas')->where('id', $ventaId)->update(['total' => $total]);
        }

        $this->resumen();
    }

    /**
     * Muestra un resumen de lo cargado para facilitar la sustentación.
     */
    private function resumen(): void
    {
        if ($this->command === null) {
            return;
        }

        $this->command->table(
            ['Tabla', 'Registros'],
            [
                ['usuarios', DB::table('usuarios')->count()],
                ['categorias', DB::table('categorias')->count()],
                ['proveedores', DB::table('proveedores')->count()],
                ['clientes', DB::table('clientes')->count()],
                ['productos', DB::table('productos')->count()],
                ['compras', DB::table('compras')->count()],
                ['detalles_compras', DB::table('detalles_compras')->count()],
                ['ventas', DB::table('ventas')->count()],
                ['detalles_ventas', DB::table('detalles_ventas')->count()],
            ]
        );
    }
}
