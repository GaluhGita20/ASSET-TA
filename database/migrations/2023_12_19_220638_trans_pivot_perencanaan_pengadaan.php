<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TransPivotPerencanaanPengadaan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('trans_pivot_perencanaan_pengadaan', function (Blueprint $table) {
            // $table->id();
            $table->unsignedBigInteger('pembelian_id');
            $table->unsignedBigInteger('detail_usulan_id');
            //$table->enum('jenis_aset',['Tanah','Gedung Bangunan','Peralatan Mesin','Aset Tetap Lainya','Jalan Irigasi Jaringan']);
            // $table->timestamps();
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
    }
}
