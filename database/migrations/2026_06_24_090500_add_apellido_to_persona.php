<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApellidoToPersona extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('persona', 'apellido')) {
            Schema::table('persona', function (Blueprint $table) {
                $table->string('apellido', 100)->nullable()->after('nombre');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('persona', 'apellido')) {
            Schema::table('persona', function (Blueprint $table) {
                $table->dropColumn('apellido');
            });
        }
    }
}
