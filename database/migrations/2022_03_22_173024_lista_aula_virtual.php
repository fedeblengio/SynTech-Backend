<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ListaAulaVirtual extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lista_aula_virtual', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_clase');
            $table->unsignedBigInteger('id_alumno');
            $table->boolean('asistencia');
            $table->unique(['id_alumno','id_clase']);
            $table->timestamps();
            
        });

        Schema::table('lista_aula_virtual', function(Blueprint $table) {
            $table->foreign('id_clase')->references('id')->on('agenda_clase_virtual');
            $table->foreign('id_alumno')->references('id')->on('alumnos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lista_aula_virtual');
    }
}
