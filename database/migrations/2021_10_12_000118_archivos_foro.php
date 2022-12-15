<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ArchivosForo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archivos_foro', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_foro');
            $table->unsignedBigInteger('id_dato');
            $table->string('nombre_archivo')->nullable();
            $table->timestamps();

        });
        Schema::table('archivos_foro', function(Blueprint $table) {
            $table->foreign('id_foro')->references('id')->on('foros');
            $table->foreign('id_dato')->references('id')->on('datosForo');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archivos_foro');
    }
}
