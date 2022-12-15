<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnosPertenecenGruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('alumnos_pertenecen_grupos', function (Blueprint $table) {
            
           
           $table->id();
            $table->unsignedBigInteger('id_grupo');
            $table->unsignedBigInteger('id_alumno');
           
            $table->unique(['id_alumno', 'id_grupo']);
           
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('alumnos_pertenecen_grupos', function(Blueprint $table) {
            $table->foreign('id_grupo')->references('id')->on('grupos');
            $table->foreign('id_alumno')->references('id')->on('alumnos');
        }); 
    }





    /**
     * 
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumnos_pertenecen_grupos');
    }
}
