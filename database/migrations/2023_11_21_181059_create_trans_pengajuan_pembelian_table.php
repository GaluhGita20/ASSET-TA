<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransPengajuanPembelianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trans_pengajuan_pembelian', function (Blueprint $table) {
            $table->id();
            $table->string("code", 30)->nullable()->unique();
            $table->date('date');
            $table->unsignedBigInteger("user_kepada")->nullable();
            $table->unsignedBigInteger("struct_id");
            $table->string("regarding")->nullable();
            $table->longText("sentence_start")->nullable();
            $table->longText("sentence_end")->nullable();
            $table->longText("note_disposisi")->nullable();
            // verification
            $table->unsignedBigInteger("verification_user_id")->nullable();
            $table->string('verification_status')->nullable();
            $table->timestamp('verification_at')->nullable();
            $table->string('status')
                ->default('new')
                ->comment('new|draft|waiting.verification|waiting.approval|completed');
            $table->longText('upgrade_reject')->nullable();
            $table->smallInteger('version')->default(0);
            $table->commonFields();

            $table->foreign("struct_id")->references("id")->on("ref_org_structs");
            $table->foreign("verification_user_id")->references("id")->on("sys_users");
        });

        Schema::create(
            'trans_pengajuan_pembelian_details',
            function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelian_id');
                $table->unsignedBigInteger('coa_id');
                $table->unsignedBigInteger('existing_amount')->nullable();
                $table->unsignedBigInteger('requirement_standard')->nullable();
                $table->unsignedBigInteger('qty_req')->nullable();
                $table->commonFields();

                // $table->primary('uuid');
                $table->foreign('coa_id')->references('id')->on('ref_coa');
                $table->foreign('pembelian_id')->references('id')->on('trans_pengajuan_pembelian');
            }
        );

        Schema::create('trans_pengajuan_pembelian_cc', function (Blueprint $table) {
            $table->unsignedBigInteger('pembelian_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('pembelian_id')->references('id')->on('trans_pengajuan_pembelian')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sys_users');

            $table->primary(['pembelian_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trans_pengajuan_pembelian');
        Schema::dropIfExists('trans_pengajuan_pembelian_details');
        Schema::dropIfExists('trans_pengajuan_pembelian_cc');
    }
}
