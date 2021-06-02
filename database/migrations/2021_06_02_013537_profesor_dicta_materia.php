<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProfesorDictaMateria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('profesor_dicta_materia', function (Blueprint $table) {
            
            $table->string('Cedula',8);
           
            $table->unsignedBigInteger('idMateria')->primary();
            $table->integer('idProfesor');
        
            $table->timestamps();
        });
        Schema::table('alumnos_pertenecen_grupos', function(Blueprint $table) {
            $table->foreign('Cedula')->references('Cedula_Profesor')->on('profesores');
            $table->foreign('idProfesor')->references('idProfesor')->on('profesores');
            $table->foreign('idMateria')->references('idMateria')->on('materias');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumnos_pertenecen_grupos');
    }
}
