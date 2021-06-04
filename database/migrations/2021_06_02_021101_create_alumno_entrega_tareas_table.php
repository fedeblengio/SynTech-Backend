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
            $table->binary('archivo')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['idAlumnos','idTareas']);
            
        });
        Schema::table('alumno_entrega_tareas', function(Blueprint $table) {
      
            $table->foreign('idAlumnos')->references('idAlumnos')->on('alumnos');
            $table->foreign('idTareas')->references('idTareas')->on('tareas');
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
