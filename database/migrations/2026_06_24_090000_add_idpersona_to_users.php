<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdpersonaToUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'idpersona')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('idpersona')->nullable()->after('idrol');
                $table->foreign('idpersona')->references('idpersona')->on('persona');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'idpersona')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['idpersona']);
                $table->dropColumn('idpersona');
            });
        }
    }
}
