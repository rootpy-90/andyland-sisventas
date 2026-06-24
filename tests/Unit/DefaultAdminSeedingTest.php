<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DefaultAdminSeedingTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSeederTables();
    }

    public function testDatabaseSeederDoesNotOverwriteExistingAdminPassword()
    {
        $existingHash = Hash::make('custom-admin-password');

        $personaId = DB::table('persona')->insertGetId([
            'tipo_persona' => 'Administrador',
            'nombre' => 'Existing Admin',
            'email' => 'admin@andyland.com',
        ]);

        DB::table('users')->insert([
            'name' => 'existing-admin',
            'email' => 'admin@andyland.com',
            'password' => $existingHash,
            'idrol' => 2,
            'idpersona' => $personaId,
        ]);

        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);

        $admin = DB::table('users')->where('email', 'admin@andyland.com')->first();

        $this->assertSame($existingHash, $admin->password);
        $this->assertSame(1, (int) $admin->idrol);
        $this->assertSame('admin', $admin->name);
    }

    public function testDatabaseSeederCreatesMissingAdminWithDefaultPassword()
    {
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);

        $admin = DB::table('users')->where('email', 'admin@andyland.com')->first();

        $this->assertNotNull($admin);
        $this->assertTrue(Hash::check('admin123', $admin->password));
        $this->assertSame(1, (int) $admin->idrol);
    }

    public function testDatabaseSeederDoesNotOverwriteExistingClientPassword()
    {
        $existingHash = Hash::make('custom-client-password');

        $personaId = DB::table('persona')->insertGetId([
            'tipo_persona' => 'Cliente',
            'nombre' => 'Existing Client',
            'email' => 'cliente@andyland.com',
        ]);

        DB::table('users')->insert([
            'name' => 'existing-client',
            'email' => 'cliente@andyland.com',
            'password' => $existingHash,
            'idrol' => 2,
            'idpersona' => $personaId,
        ]);

        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);

        $client = DB::table('users')->where('email', 'cliente@andyland.com')->first();

        $this->assertSame($existingHash, $client->password);
        $this->assertSame(2, (int) $client->idrol);
        $this->assertSame('cliente', $client->name);
    }

    public function testDatabaseSeederCreatesMissingClientWithDefaultPassword()
    {
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);

        $client = DB::table('users')->where('email', 'cliente@andyland.com')->first();

        $this->assertNotNull($client);
        $this->assertTrue(Hash::check('cliente123', $client->password));
        $this->assertSame(2, (int) $client->idrol);
    }

    public function testMysqlInitSqlDoesNotInsertDefaultUsers()
    {
        $initSql = file_get_contents(base_path('docker/mysql/init.sql'));

        $this->assertNotContains('admin@andyland.com', $initSql);
        $this->assertNotContains('INSERT INTO `users`', $initSql);
        $this->assertNotContains('INSERT INTO `persona`', $initSql);
    }

    public function testEntrypointRunsSeederOnInstalledStartsToo()
    {
        $entrypoint = file_get_contents(base_path('docker/entrypoint.sh'));

        $this->assertContains('php artisan db:seed --force', $entrypoint);
        $this->assertContains('ADMIN_EXISTS_AFTER', $entrypoint);
        $this->assertContains('Default admin verified after seeding', $entrypoint);
    }

    private function createSeederTables()
    {
        Schema::create('roles', function ($table) {
            $table->increments('idrol');
            $table->string('nombre');
            $table->boolean('es_admin')->default(false);
            $table->string('descripcion')->nullable();
        });

        Schema::create('persona', function ($table) {
            $table->increments('idpersona');
            $table->string('tipo_persona')->default('Cliente');
            $table->string('nombre');
            $table->string('apellido')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('num_documento')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('pais')->nullable();
        });

        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->integer('idrol')->default(2);
            $table->integer('idpersona')->unsigned()->nullable();
        });

        Schema::create('categoria', function ($table) {
            $table->increments('idcategoria');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('condicion')->default(true);
        });

        Schema::create('articulo', function ($table) {
            $table->increments('idarticulo');
            $table->integer('idcategoria')->unsigned();
            $table->string('codigo')->nullable();
            $table->string('nombre');
            $table->integer('stock')->default(0);
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->string('estado')->default('Activo');
            $table->integer('tiempo_entrega')->nullable();
        });
    }
}
