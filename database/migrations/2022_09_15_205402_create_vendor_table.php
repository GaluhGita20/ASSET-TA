<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'ref_vendor',
            function (Blueprint $table) {
                $table->id();
                $table->string("id_vendor")->unique();
                $table->string("name");
                $table->string('pimpinan')->nullable();
                $table->string('kode_rekening')->nullable();
                $table->string('kode_instansi')->nullable();
                $table->string("telp")->nullable();
                $table->string("email")->nullable();
                $table->longText("address")->nullable();
                $table->string('province_id')->nullable();
                $table->string('city_id')->nullable();
                $table->string('contact_person')->nullable();
                $table->commonFields();
            }
        );

        Schema::create('ref_type_vendor_details', function (Blueprint $table) {
            $table->unsignedBigInteger('type_vendor_id');
            $table->unsignedBigInteger('vendor_id');

            $table->foreign('type_vendor_id')->references('id')->on('ref_type_vendor')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('ref_vendor');

            $table->primary(['type_vendor_id', 'vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ref_vendor');
    }
}
