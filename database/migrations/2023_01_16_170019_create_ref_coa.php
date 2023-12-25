<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefCoa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'ref_kode_aset_bmd',
            function (Blueprint $table) {
                $table->id();
                $table->string('kode_akun')->unique();
                $table->string('nama_akun')->nullable();
                $table->enum('tipe_akun', ['KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E', 'KIB F'])->nullable();
                $table->text('deskripsi')->nullable();
                $table->string('status')->nullable();
                $table->commonFields();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ref_coa');
    }
}
