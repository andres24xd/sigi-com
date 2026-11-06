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

        foreach (['Herramientas', 'Tornillería', 'Pinturas', 'Plomería'] as $nombre) {
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
            ['nombre' => 'Ferretería Central S.A.S.', 'telefono' => '3001234567'],
            ['nombre' => 'Importadora Andina de Herramientas', 'telefono' => '3012223344'],
            ['nombre' => 'Pinturas y Acabados del Norte', 'telefono' => '3023334455'],
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
            ['nombre' => 'Constructora El Roble S.A.S.', 'telefono' => '3101112233'],
            ['nombre' => 'Carlos Mendoza', 'telefono' => '3112223344'],
            ['nombre' => 'Laura Restrepo', 'telefono' => '3123334455'],
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
            ['nombre' => 'Martillo de uña 16 oz', 'categoria' => 'Herramientas', 'precio_venta' => 32000],
            ['nombre' => 'Juego de destornilladores 6 piezas', 'categoria' => 'Herramientas', 'precio_venta' => 45000],
            ['nombre' => 'Llave ajustable 10 pulgadas', 'categoria' => 'Herramientas', 'precio_venta' => 38000],
            ['nombre' => 'Tornillo autoperforante 8 x 1" (caja x 100)', 'categoria' => 'Tornillería', 'precio_venta' => 18000],
            ['nombre' => 'Chazo plástico 1/4" (caja x 50)', 'categoria' => 'Tornillería', 'precio_venta' => 12000],
            ['nombre' => 'Bisagra zincada 3" (par)', 'categoria' => 'Tornillería', 'precio_venta' => 9500],
            ['nombre' => 'Pintura acrílica blanca 1 galón', 'categoria' => 'Pinturas', 'precio_venta' => 78000],
            ['nombre' => 'Brocha de cerda 3"', 'categoria' => 'Pinturas', 'precio_venta' => 14500],
            ['nombre' => 'Thinner corriente 1 litro', 'categoria' => 'Pinturas', 'precio_venta' => 11000],
            ['nombre' => 'Tubo PVC presión 1/2" x 6 m', 'categoria' => 'Plomería', 'precio_venta' => 26000],
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
                'proveedor' => 'Ferretería Central S.A.S.',
                'fecha' => $ahora->copy()->subDays(4)->setTime(8, 0),
                'detalles' => [
                    ['producto' => 'Martillo de uña 16 oz', 'cantidad' => 30, 'precio_unitario' => 22000],
                    ['producto' => 'Juego de destornilladores 6 piezas', 'cantidad' => 20, 'precio_unitario' => 32000],
                    ['producto' => 'Llave ajustable 10 pulgadas', 'cantidad' => 25, 'precio_unitario' => 27000],
                    ['producto' => 'Tornillo autoperforante 8 x 1" (caja x 100)', 'cantidad' => 40, 'precio_unitario' => 12000],
                    ['producto' => 'Chazo plástico 1/4" (caja x 50)', 'cantidad' => 60, 'precio_unitario' => 8000],
                ],
            ],
            [
                'proveedor' => 'Importadora Andina de Herramientas',
                'fecha' => $ahora->copy()->subDays(3)->setTime(8, 30),
                'detalles' => [
                    ['producto' => 'Bisagra zincada 3" (par)', 'cantidad' => 12, 'precio_unitario' => 6500],
                    ['producto' => 'Tubo PVC presión 1/2" x 6 m', 'cantidad' => 50, 'precio_unitario' => 18000],
                ],
            ],
            [
                'proveedor' => 'Pinturas y Acabados del Norte',
                'fecha' => $ahora->copy()->subDays(2)->setTime(9, 0),
                'detalles' => [
                    ['producto' => 'Pintura acrílica blanca 1 galón', 'cantidad' => 24, 'precio_unitario' => 58000],
                    ['producto' => 'Brocha de cerda 3"', 'cantidad' => 8, 'precio_unitario' => 9500],
                    ['producto' => 'Thinner corriente 1 litro', 'cantidad' => 10, 'precio_unitario' => 7500],
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
                'cliente' => 'Constructora El Roble S.A.S.',
                'usuario' => 'cajero@sigicom.com',
                'fecha' => $ahora->copy()->subDays(3)->setTime(10, 15),
                'detalles' => [
                    ['producto' => 'Martillo de uña 16 oz', 'cantidad' => 3, 'precio_unitario' => 32000],
                    ['producto' => 'Tornillo autoperforante 8 x 1" (caja x 100)', 'cantidad' => 5, 'precio_unitario' => 18000],
                    ['producto' => 'Bisagra zincada 3" (par)', 'cantidad' => 4, 'precio_unitario' => 9500],
                ],
            ],
            [
                'cliente' => null, // Consumidor final
                'usuario' => 'cajero@sigicom.com',
                'fecha' => $ahora->copy()->subDays(2)->setTime(16, 40),
                'detalles' => [
                    ['producto' => 'Pintura acrílica blanca 1 galón', 'cantidad' => 1, 'precio_unitario' => 78000],
                    ['producto' => 'Brocha de cerda 3"', 'cantidad' => 2, 'precio_unitario' => 14500],
                ],
            ],
            [
                'cliente' => 'Carlos Mendoza',
                'usuario' => 'admin@sigicom.com',
                'fecha' => $ahora->copy()->subDay()->setTime(11, 20),
                'detalles' => [
                    ['producto' => 'Juego de destornilladores 6 piezas', 'cantidad' => 1, 'precio_unitario' => 45000],
                    ['producto' => 'Llave ajustable 10 pulgadas', 'cantidad' => 1, 'precio_unitario' => 38000],
                    ['producto' => 'Tubo PVC presión 1/2" x 6 m', 'cantidad' => 2, 'precio_unitario' => 26000],
                ],
            ],
            [
                'cliente' => 'Laura Restrepo',
                'usuario' => 'cajero@sigicom.com',
                'fecha' => $ahora->copy()->setTime(9, 5),
                'detalles' => [
                    ['producto' => 'Chazo plástico 1/4" (caja x 50)', 'cantidad' => 2, 'precio_unitario' => 12000],
                    ['producto' => 'Thinner corriente 1 litro', 'cantidad' => 1, 'precio_unitario' => 11000],
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
