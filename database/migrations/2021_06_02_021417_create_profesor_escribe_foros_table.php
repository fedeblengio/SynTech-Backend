<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfesorEscribeForosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('profesor_estan_grupo_foro', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_foro')->unique();
            $table->unsignedBigInteger('id_materia');
            $table->unsignedBigInteger('id_grupo');
            $table->unsignedBigInteger('id_profesor');
           
            
        
            $table->timestamps();
        });
        Schema::table('profesor_estan_grupo_foro', function(Blueprint $table) {
            $table->foreign('id_foro')->references('id')->on('foros');
            $table->foreign('id_grupo')->references('id')->on('grupos');
            $table->foreign('id_materia')->references('id')->on('materias');
            $table->foreign('id_profesor')->references('id')->on('profesores');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profesor_estan_grupo_foro');
    }
}
