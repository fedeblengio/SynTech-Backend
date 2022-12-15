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
           
            $table->id();
            $table->string('cedula_alumno',8)->unique();
            $table->timestamps();
            $table->softDeletes();
        });
         
        Schema::table('alumnos', function(Blueprint $table) {
            $table->foreign('cedula_alumno')->references('cedula')->on('usuarios');
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
