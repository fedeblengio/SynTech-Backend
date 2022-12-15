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
            $table->id();
            $table->unsignedBigInteger('id_materia');
            $table->unsignedBigInteger('id_profesor');
            $table->unique(['id_materia', 'id_profesor']);
           
            
        
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('profesor_dicta_materia', function(Blueprint $table) {
            $table->foreign('id_profesor')->references('id')->on('profesores');
            $table->foreign('id_materia')->references('id')->on('materias');
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
