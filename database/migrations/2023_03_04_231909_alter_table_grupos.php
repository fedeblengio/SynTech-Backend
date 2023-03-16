<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableGrupos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grupos', function (Blueprint $table) {
      
            $table->dropForeign(['id_grado']);
            $table->dropColumn('id_grado');
            $table->unsignedBigInteger('grado_id')->nullable();
            $table->foreign('grado_id')->references('id')->on('grados');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropForeign('grado_id');
            $table->dropColumn('grado_id');
          
            $table->foreign('id_grado')->references('id')->on('grados');
        }); 
    }
}
