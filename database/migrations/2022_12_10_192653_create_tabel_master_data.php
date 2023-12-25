<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabelMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_aset', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->enum('jenis_aset',['Tanah','Gedung Bangunan','Peralatan Mesin','Aset Tetap Lainya','Jalan Irigasi Jaringan']);
            $table->timestamps();
        });

        Schema::create('ref_jenis_pemutihan', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            //$table->enum('jenis_aset',['Tanah','Gedung Bangunan','Peralatan Mesin','Aset Tetap Lainya','Jalan Irigasi Jaringan']);
            $table->timestamps();
        });

        Schema::create('ref_jenis_pengadaan', function (Blueprint $table) {
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
        //Schema::dropIfExists('tabel_master_data');
        Schema::dropIfExists('ref_aset');
        Schema::dropIfExists('ref_jenis_pemutihan');
        Schema::dropIfExists('ref_jenis_pengadaan');
    }

   
}
