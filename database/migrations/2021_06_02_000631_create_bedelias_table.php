<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBedeliasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bedelias', function (Blueprint $table) {
            $table->integer("idBedelias");
            $table->string('Cedula_Bedelia',8);
            $table->primary(['Cedula_Bedelia', 'idBedelias']);
            $table->string("cargo")->nullable();
            $table->timestamps();
        });

        Schema::table('bedelias', function(Blueprint $table) {
            $table->foreign('Cedula_Bedelia')->references('username')->on('usuarios');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bedelias');
    }
}
