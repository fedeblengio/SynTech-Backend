<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GruposPertenecenCarrera extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos_pertenecen_carrera', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrera_id');
            $table->unsignedBigInteger('grado_id');
            $table->unsignedBigInteger('grupo_id');
            $table->foreign('carrera_id')->references('id')->on('carreras');
            $table->foreign('grupo_id')->references('id')->on('grupos');
            $table->foreign('grado_id')->references('id')->on('grados');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupos_pertenecen_carrera');
    }
}
