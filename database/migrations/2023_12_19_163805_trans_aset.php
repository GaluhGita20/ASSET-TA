<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TransAset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('trans_aset', function (Blueprint $table) {
            $table->id();
            $table->string('trans_name');
            $table->unsignedBigInteger('ref_vendor');
            $table->unsignedBigInteger('ref_jenis_pengadaan');
            $table->string('no_spk',100);
            $table->dateTime('spk_start_date');
            $table->dateTime('spk_end_date');
            $table->unsignedInteger('spk_range_time');
            $table->double('budget_limit');
            $table->unsignedInteger('qty');
            $table->double('unit_cost');
            $table->double('shiping_cost');
            $table->double('tax_cost');
            $table->dateTime('receipt_date');
            $table->string('faktur_code',100);
            $table->string('location_receipt',255);
            $table->string('sp2d_code',100);
            $table->dateTime('sp2d_date');
            $table->enum('condition_aset',['Baik','Rusak']);
            $table->text('asset_test_results');
            $table->string('status')
                ->default('new')
                ->comment('new|draft|waiting.verification|waiting.approval|completed');
            //$table->enum('jenis_aset',['Tanah','Gedung Bangunan','Peralatan Mesin','Aset Tetap Lainya','Jalan Irigasi Jaringan']);
            $table->commonFields();

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
