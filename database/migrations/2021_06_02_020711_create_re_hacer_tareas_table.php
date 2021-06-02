<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReHacerTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('re_hacer_tareas', function (Blueprint $table) {
            $table->unsignedBigInteger('idTareasNueva');
            $table->unsignedBigInteger('idTareas');
            $table->string('calificacion')->nullable();
            $table->string('fechaEntrega')->nullable();
            $table->binary('arhivo')->nullable();
            $table->primary(['idTareasNueva', 'idTareas']);
        
            $table->timestamps();


        });
        Schema::table('re_hacer_tareas', function(Blueprint $table) {
            $table->foreign('idTareas')->references('idTareas')->on('tareas');
            $table->foreign('idTareasNueva')->references('idTareas')->on('tareas');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('re_hacer_tareas');
    }
}
