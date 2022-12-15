<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReHacerTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('re_hacer_tareas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tarea_nueva');
            $table->unsignedBigInteger('id_tarea');
            $table->string('calificacion')->nullable();
            $table->unique(['id_tarea_nueva', 'id_tarea']);
            $table->unsignedBigInteger('id_alumno');
            $table->string('mensaje')->nullable();
            $table->string('mensaje_profesor')->nullable();
            $table->timestamps();
            
            

        });
        Schema::table('re_hacer_tareas', function(Blueprint $table) {
            $table->foreign('id_tarea')->references('id')->on('tareas');
            $table->foreign('id_tarea_nueva')->references('id')->on('tareas');
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
        Schema::dropIfExists('re_hacer_tareas');
    }
}
