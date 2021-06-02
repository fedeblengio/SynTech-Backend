<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
   
        Schema::create('alumnos', function (Blueprint $table) {
           
            $table->integer('idAlumnos');
            //$table->foreign('Cedula')->references('username')->on('usuarios')->onDelete('cascade');
            $table->string('Cedula_Alumno',8);
            $table->primary(['idAlumnos', 'Cedula_Alumno']);
            $table->timestamps();
        });
         
        Schema::table('alumnos', function(Blueprint $table) {
            $table->foreign('Cedula_Alumno')->references('username')->on('usuarios');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumnos');
    }
}
