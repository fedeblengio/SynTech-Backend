<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialRegistrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historial_registros', function (Blueprint $table) {
            $table->id();
            $table->string('idUsuario', 8);
            $table->string('App');
            $table->string('Accion');
            $table->string('Mensaje');
            $table->timestamps();
        });
        Schema::table('historial_registros', function (Blueprint $table) {
            $table->foreign('idUsuario')->references('username')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial_registros');
    }
}
