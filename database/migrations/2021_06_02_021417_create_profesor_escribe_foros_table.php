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
        Schema::create('profesor_escribe_foro', function (Blueprint $table) {
            $table->unsignedBigInteger('idForo')->primary();
            $table->unsignedBigInteger('idMateria');
            $table->string('idGrupo',10);
            $table->integer('idProfesor');
           
            
        
            $table->timestamps();
        });
        Schema::table('profesor_escribe_foro', function(Blueprint $table) {
            $table->foreign('idForo')->references('id')->on('foros');
            $table->foreign('idGrupo')->references('idGrupo')->on('grupos');
            $table->foreign('idMateria')->references('id')->on('materias');
            $table->foreign('idProfesor')->references('idProfesor')->on('profesores');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profesor_escribe_foros');
    }
}
