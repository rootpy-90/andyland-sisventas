<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use sisVentas\User;
use sisVentas\Persona;
use sisVentas\Categoria;
use sisVentas\Articulo;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // ============================================
        // Crear roles (si no existen)
        // ============================================
        DB::table('roles')->updateOrInsert(
            ['idrol' => 1],
            ['nombre' => 'Administrador', 'es_admin' => 1, 'descripcion' => 'Acceso completo al sistema']
        );
        DB::table('roles')->updateOrInsert(
            ['idrol' => 2],
            ['nombre' => 'Cliente', 'es_admin' => 0, 'descripcion' => 'Acceso solo a tienda y perfil']
        );

        // ============================================
        // Crear usuario admin (idempotente)
        // ============================================
        $adminPersona = Persona::updateOrCreate(
            ['email' => 'admin@andyland.com'],
            [
                'tipo_persona' => 'Administrador',
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'tipo_documento' => 'DNI',
                'num_documento' => '0000000',
                'direccion' => 'Oficina Central',
                'telefono' => '000000000',
                'ciudad' => 'Asunción',
                'pais' => 'Paraguay',
            ]
        );

        $adminUser = User::where('email', 'admin@andyland.com')->first();

        if ($adminUser) {
            $adminUser->name = 'admin';
            $adminUser->idrol = 1;
            $adminUser->idpersona = $adminPersona->idpersona;
            $adminUser->save();
            $adminWasCreated = false;
        } else {
            $adminUser = User::create([
                'name' => 'admin',
                'email' => 'admin@andyland.com',
                'password' => Hash::make('admin123'),
                'idrol' => 1,
                'idpersona' => $adminPersona->idpersona,
            ]);
            $adminWasCreated = true;
        }

        // ============================================
        // Crear usuario cliente (idempotente)
        // ============================================
        $clientePersona = Persona::updateOrCreate(
            ['email' => 'cliente@andyland.com'],
            [
                'tipo_persona' => 'Cliente',
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'tipo_documento' => 'DNI',
                'num_documento' => '1234567',
                'direccion' => 'Calle Falsa 123',
                'telefono' => '0981234567',
                'ciudad' => 'Asunción',
                'pais' => 'Paraguay',
            ]
        );

        $clienteUser = User::where('email', 'cliente@andyland.com')->first();

        if ($clienteUser) {
            $clienteUser->name = 'cliente';
            $clienteUser->idrol = 2;
            $clienteUser->idpersona = $clientePersona->idpersona;
            $clienteUser->save();
            $clienteWasCreated = false;
        } else {
            $clienteUser = User::create([
                'name' => 'cliente',
                'email' => 'cliente@andyland.com',
                'password' => Hash::make('cliente123'),
                'idrol' => 2,
                'idpersona' => $clientePersona->idpersona,
            ]);
            $clienteWasCreated = true;
        }

        // ============================================
        // Crear proveedor (idempotente)
        // ============================================
        Persona::updateOrCreate(
            ['email' => 'proveedor@andyland.com'],
            [
                'tipo_persona' => 'Proveedor',
                'nombre' => 'Distribuidora SA',
                'tipo_documento' => 'RUC',
                'num_documento' => '80012345-6',
                'direccion' => 'Av. Principal 456',
                'telefono' => '021456789',
                'ciudad' => 'Asunción',
                'pais' => 'Paraguay',
            ]
        );

        // ============================================
        // Crear categorías (idempotente)
        // ============================================
        $categoria1 = Categoria::updateOrCreate(
            ['nombre' => 'Electrónica'],
            ['descripcion' => 'Productos electrónicos', 'condicion' => 1]
        );

        $categoria2 = Categoria::updateOrCreate(
            ['nombre' => 'Ropa'],
            ['descripcion' => 'Prendas de vestir', 'condicion' => 1]
        );

        $categoria3 = Categoria::updateOrCreate(
            ['nombre' => 'Hogar'],
            ['descripcion' => 'Artículos para el hogar', 'condicion' => 1]
        );

        // ============================================
        // Crear artículos de prueba (solo si no existen)
        // ============================================
        $articulosExistentes = Articulo::count();
        
        if ($articulosExistentes === 0) {
            // Electrónica (10 artículos)
            for ($i = 1; $i <= 10; $i++) {
                Articulo::create([
                    'idcategoria' => $categoria1->idcategoria,
                    'codigo' => 'ART-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'nombre' => 'Producto Electrónico ' . $i,
                    'stock' => rand(10, 100),
                    'descripcion' => 'Descripción del producto electrónico ' . $i,
                    'imagen' => '',
                    'estado' => 'Activo',
                    'tiempo_entrega' => rand(1, 7),
                ]);
            }

            // Ropa (5 artículos)
            for ($i = 11; $i <= 15; $i++) {
                Articulo::create([
                    'idcategoria' => $categoria2->idcategoria,
                    'codigo' => 'ART-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'nombre' => 'Prenda de Ropa ' . ($i - 10),
                    'stock' => rand(20, 50),
                    'descripcion' => 'Descripción de la prenda de ropa ' . ($i - 10),
                    'imagen' => '',
                    'estado' => 'Activo',
                    'tiempo_entrega' => rand(1, 5),
                ]);
            }

            // Hogar (5 artículos)
            for ($i = 16; $i <= 20; $i++) {
                Articulo::create([
                    'idcategoria' => $categoria3->idcategoria,
                    'codigo' => 'ART-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'nombre' => 'Artículo para Hogar ' . ($i - 15),
                    'stock' => rand(5, 30),
                    'descripcion' => 'Descripción del artículo para el hogar ' . ($i - 15),
                    'imagen' => '',
                    'estado' => 'Activo',
                    'tiempo_entrega' => rand(2, 10),
                ]);
            }
        }

        // ============================================
        // Mensaje de éxito
        // ============================================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('✓ Seeders ejecutados correctamente');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('Usuarios iniciales:');
        if ($adminWasCreated) {
            $this->command->info('  Admin:   admin@andyland.com / admin123 (creado)');
        } else {
            $this->command->info('  Admin:   admin@andyland.com (ya existía, contraseña preservada)');
        }
        if ($clienteWasCreated) {
            $this->command->info('  Cliente: cliente@andyland.com / cliente123 (creado)');
        } else {
            $this->command->info('  Cliente: cliente@andyland.com (ya existía, contraseña preservada)');
        }
        $this->command->info('');
        $this->command->info('Datos creados:');
        $this->command->info('  Roles: 2');
        $this->command->info('  Categorías: 3');
        $this->command->info('  Artículos: ' . Articulo::count());
        $this->command->info('');
    }
}
