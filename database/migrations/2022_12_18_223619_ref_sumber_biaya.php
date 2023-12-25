<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefSumberBiaya extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('ref_sumber_biaya', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            //$table->enum('jenis_aset',['Tanah','Gedung Bangunan','Peralatan Mesin','Aset Tetap Lainya','Jalan Irigasi Jaringan']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('ref_sumber_biaya');
    }
}
