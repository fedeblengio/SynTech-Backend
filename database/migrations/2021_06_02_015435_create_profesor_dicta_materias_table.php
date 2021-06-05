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
            $table->unsignedBigInteger('idMateria');
            $table->integer('idProfesor');
            $table->primary(['idMateria', 'idProfesor']);
           
            
        
            $table->timestamps();
        });
        Schema::table('profesor_dicta_materia', function(Blueprint $table) {
            $table->foreign('idProfesor')->references('idProfesor')->on('profesores');
            $table->foreign('idMateria')->references('id')->on('materias');
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
