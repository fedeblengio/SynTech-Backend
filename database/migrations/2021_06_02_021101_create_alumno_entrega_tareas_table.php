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
            
          
            $table->unsignedBigInteger('idTareas');
            $table->integer('idAlumnos');
            $table->string('calificacion')->nullable();
            /* $table->binary('archivo')->nullable(); */
            $table->string('mensaje')->nullable();
            $table->timestamps();
            $table->primary(['idAlumnos','idTareas']);
            
        });
        Schema::table('alumno_entrega_tareas', function(Blueprint $table) {
      
            $table->foreign('idAlumnos')->references('idAlumnos')->on('alumnos');
            $table->foreign('idTareas')->references('id')->on('tareas');
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