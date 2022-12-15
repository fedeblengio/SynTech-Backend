<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGruposTienenProfesorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos_tienen_profesor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_materia');
            $table->unsignedBigInteger('id_grupo');
            $table->unsignedBigInteger('id_profesor');
            $table->unique(['id_grupo', 'id_profesor', 'id_materia']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('grupos_tienen_profesor', function (Blueprint $table) {
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
        Schema::dropIfExists('grupos_tienen_profesors');
    }
}
