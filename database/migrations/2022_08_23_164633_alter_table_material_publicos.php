<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableMaterialPublicos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_publicos', function(Blueprint $table) {
            $table->binary('imgEncabezado');  
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_publicos', function($table) {
            $table->dropColumn('imgEncabezado');  
            
        });
    }
}
