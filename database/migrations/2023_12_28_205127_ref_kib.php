<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('ref_kib', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trans_id');
            $table->unsignedBigInteger('departemen_id');
            $table->unsignedBigInteger('no_register');
            $table->double('land_area_m2');
            $table->unsignedBigInteger('id_provinsi');
            $table->unsignedBigInteger('id_kabupaten');
            $table->unsignedBigInteger('id_daerah');
            $table->string('land_rights');
            $table->string('no_sertificate');
            $table->date('sertificate_date');
            $table->string('land_use');

            $table->string('merek_type_item');
            $table->double('cc_size_item');
            $table->string('material_item');
            $table->string('no_factory_item');
            $table->string('no_police_item');
            $table->string('no_BPKB_item');
            $table->string('no_machine_item');

            $table->enum('is_graded_bld',['yes','no']);
            $table->enum('is_concreate_bld',['yes','no']);
            $table->double('floor_area_m2_bld');
            $table->string('no_document_bld');
            $table->string('date_document_bld');

            $table->double('long_JJR');
            $table->double('width_JJR');
            $table->double('wide_JJR');

            $table->string('title_book_ATL');
            $table->string('spesification_book_ATL');
            $table->string('publication_year_book_ATL');
            $table->string('type_animal_ATL');
            $table->string('size_animal_ATL');
            $table->string('creator_art_ATL');
            $table->string('material_art_ATL');

            $table->string('is_graded_KP');
            $table->string('is_conrete_KP');
            $table->string('wide_KP');
            $table->enum('status',['active','notactive','diputihkan']);
            $table->enum('condition',['baik','rusak berat','rusak sedang']);
            $table->double('room_location');
            $table->string('location');
            $table->double('ref_kib_tanah');

            //$table->enum('jenis_aset',['Tanah','Gedung Bangunan','Peralatan Mesin','Aset Tetap Lainya','Jalan Irigasi Jaringan']);
            $table->commonFields();

        });

        Schema::create('sys_penyusutan_aset', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kib_id');
            $table->double('acquisition_val');
            $table->double('residual_val');
            $table->double('depreciation_rate');
            $table->year('depreciation_period');
            $table->double('depreciation_val');
            $table->double('final_value_recorded');
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
};
