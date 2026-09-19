<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // 1. Deshabilitar temporalmente la verificación de claves foráneas
            Schema::disableForeignKeyConstraints();

            // 2. Limpieza de tablas (DDL) antes de iniciar la transacción en MySQL
            DB::table('detalles_ventas')->truncate();
            DB::table('ventas')->truncate();
            DB::table('detalles_compras')->truncate();
            DB::table('compras')->truncate();
            DB::table('productos')->truncate();
            DB::table('clientes')->truncate();
            DB::table('proveedores')->truncate();
            DB::table('categorias')->truncate();
            DB::table('usuarios')->truncate();

            // 3. Iniciar la transacción para proteger la carga de datos
            DB::beginTransaction();

            // --- Carga de Usuarios ---
            $usuarios = [
                [
                    'nombre' => 'Administrador General',
                    'email' => 'admin@sigicom.com',
                    'rol' => 'Administrador',
                    'password' => Hash::make('admin123'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nombre' => 'Cajero Mostrador',
                    'email' => 'cajero@sigicom.com',
                    'rol' => 'Cajero',
                    'password' => Hash::make('cajero123'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];
            DB::table('usuarios')->insert($usuarios);

            // --- Carga de Categorías ---
            $categorias = [
                ['nombre' => 'Abarrotes', 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Bebidas', 'created_at' => now(), 'updated_at' => now()],
            ];
            DB::table('categorias')->insert($categorias);

            // --- Carga de Proveedores ---
            $proveedores = [
                ['nombre' => 'Distribuidora Central S.A.S.', 'telefono' => '3001234567', 'created_at' => now(), 'updated_at' => now()],
            ];
            DB::table('proveedores')->insert($proveedores);

            // --- Carga de Clientes ---
            $clientes = [
                ['nombre' => 'Consumidor Final', 'telefono' => '3109876543', 'created_at' => now(), 'updated_at' => now()],
            ];
            DB::table('clientes')->insert($clientes);

            // 4. Confirmar la transacción si todo se insertó correctamente
            DB::commit();

        } catch (\Throwable $th) {
            // Revertir los cambios si ocurre algún error durante la inserción
            DB::rollBack();
            dump('Error durante la inserción del Seeder: ' . $th->getMessage());
        } finally {
            // Habilitar de nuevo las claves foráneas obligatoriamente
            Schema::enableForeignKeyConstraints();
        }
    }
}