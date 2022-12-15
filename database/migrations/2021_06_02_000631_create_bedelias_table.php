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
            $table->id();
            $table->string('cedula_bedelia',8)->unique();
            $table->string("cargo")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('bedelias', function(Blueprint $table) {
            $table->foreign('cedula_bedelia')->references('cedula')->on('usuarios');
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
