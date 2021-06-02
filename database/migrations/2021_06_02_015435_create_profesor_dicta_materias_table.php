<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfesorDictaMateriasTable extends Migration
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
            $table->unsignedBigInteger('idMateria')->primary();
            $table->integer('idProfesor');
            $table->string('Cedula',8);
           
            
        
            $table->timestamps();
        });
        Schema::table('profesor_dicta_materia', function(Blueprint $table) {
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
        Schema::dropIfExists('profesor_dicta_materia');
    }
}
