<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ArchivosEntrega extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archivos_entrega', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tarea');
            $table->unsignedBigInteger('id_alumno');
            $table->string('nombre_archivo')->nullable();
            $table->timestamps();
        });
        Schema::table('archivos_entrega', function(Blueprint $table) {
            $table->foreign('id_tarea')->references('id')->on('tareas');
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
        Schema::dropIfExists('archivos_entrega');
    }
}
