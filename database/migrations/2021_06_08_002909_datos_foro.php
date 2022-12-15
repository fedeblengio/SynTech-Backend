<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DatosForo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datosForo', function (Blueprint $table) {
            $table->unsignedBigInteger('id_foro');
            $table->id();
            $table->string('cedula',8);
            $table->binary('mensaje');
            $table->timestamps();
            
        });

        Schema::table('datosForo', function(Blueprint $table) {
            $table->foreign('id_foro')->references('id')->on('foros');
            $table->foreign('cedula')->references('cedula')->on('usuarios');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('datosForo');
    }
}
