<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnoEntregaTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('alumno_entrega_tareas', function (Blueprint $table) {
            $table->id();
           
            $table->unsignedBigInteger('id_tarea');
            $table->unsignedBigInteger('id_alumno');
            $table->string('calificacion')->nullable();
            
            $table->string('mensaje_profesor')->nullable();
            $table->boolean('re_hacer');
            $table->string('mensaje')->nullable();
            $table->unique(['id_alumno','id_tarea']);
            $table->timestamps();
            
        });
        Schema::table('alumno_entrega_tareas', function(Blueprint $table) {
           
            $table->foreign('id_alumno')->references('id')->on('alumnos');
            $table->foreign('id_tarea')->references('id')->on('profesor_crea_tareas');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumno_entrega_tareas');
    }
}