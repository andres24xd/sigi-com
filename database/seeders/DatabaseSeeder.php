<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Usuarios del sistema (Admin y Cajero)
        DB::table('users')->insert([
            [
                'name' => 'Administrador General',
                'email' => 'admin@sigicom.com',
                'role' => 'Administrador',
                'password' => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cajero Mostrador',
                'email' => 'cajero@sigicom.com',
                'role' => 'Cajero',
                'password' => Hash::make('cajero123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Categorías
        $catAbarrotes = DB::table('categories')->insertGetId([
            'name' => 'Abarrotes',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $catBebidas = DB::table('categories')->insertGetId([
            'name' => 'Bebidas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Proveedores
        $supplierId = DB::table('suppliers')->insertGetId([
            'name' => 'Distribuidora Comercial S.A.S.',
            'phone' => '3001234567',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Clientes
        $customerId = DB::table('customers')->insertGetId([
            'name' => 'Consumidor Frecuente',
            'phone' => '3109876543',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Catálogo de Productos
        DB::table('products')->insert([
            [
                'name' => 'Arroz Diana 1kg',
                'category_id' => $catAbarrotes,
                'sale_price' => 4500.00,
                'current_stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gaseosa Postobón 1.5L',
                'category_id' => $catBebidas,
                'sale_price' => 4000.00,
                'current_stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}