<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfesoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profesores', function (Blueprint $table) {

            $table->integer("idProfesor");
            $table->string('Cedula_Profesor',8);
            $table->primary(['Cedula_Profesor', 'idProfesor']);
            $table->string("grado")->nullable();
            $table->timestamps();
        });
        Schema::table('profesores', function(Blueprint $table) {
            $table->foreign('Cedula_Profesor')->references('username')->on('usuarios');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profesores');
    }
}
