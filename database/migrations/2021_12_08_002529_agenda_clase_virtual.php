<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AgendaClaseVirtual extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('agenda_clase_virtual', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_profesor');
            $table->unsignedBigInteger('id_materia');
            $table->unsignedBigInteger('id_grupo');
            $table->string('fecha_inicio');
            $table->string('fecha_fin');
            $table->timestamps();
        });

        Schema::table('agenda_clase_virtual', function (Blueprint $table) {
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
        Schema::dropIfExists('agenda_clase_virtual');
    }
}
