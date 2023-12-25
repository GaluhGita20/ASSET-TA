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
        Schema::create('trans_usulan', function (Blueprint $table) {
            $table->id();
            $table->string("code", 30)->nullable()->unique();
            $table->date('date');
            $table->enum('is_repair', ['yes', 'no']);
           // $table->unsignedBigInteger("user_kepada")->nullable();
            $table->unsignedBigInteger("struct_id");
            $table->string("regarding")->nullable();
            $table->longText("sentence_start")->nullable();
            $table->longText("sentence_end")->nullable();
            $table->longText("note")->nullable();
            $table->string('procurement_year')->nullable();
            // verification
            // $table->unsignedBigInteger("verification_user_id")->nullable();
           // $table->string('verification_status')->nullable();
            //$table->timestamp('verification_at')->nullable();
            $table->string('status')
                ->default('new')
                ->comment('new|draft|waiting.verification|waiting.approval|completed');
            $table->longText('upgrade_reject')->nullable();
            $table->smallInteger('version')->default(0);
            $table->commonFields();

            $table->foreign("struct_id")->references("id")->on("ref_org_structs");
            // $table->foreign("verification_user_id")->references("id")->on("sys_users");
        });

        Schema::create(
            'trans_usulan_details',
            function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('usulan_id');
                $table->unsignedBigInteger('aset_id');
                $table->enum('is_bangunan',['yes', 'no']);
                $table->string('desc_spesification');

                $table->unsignedBigInteger('existing_amount')->nullable();
                $table->unsignedBigInteger('requirement_standard')->nullable();
                $table->unsignedBigInteger('qty_req')->nullable();
                $table->double('HPS_unit_cost',20,2)->nullable();
                $table->double('HPS_total_cost',20,2)->nullable();
                $table->enum('status',['draf','waiting purchase','waiting register','completed']);
                $table->unsignedInteger('qty_agree')->nullable();
                $table->double('HPS_total_agree',20,2)->nullable();
                $table->unsignedBigInteger('sumber_biaya_id')->nullable();
                $table->commonFields();

                // $table->primary('uuid');
                $table->foreign('sumber_biaya_id')->references('id')->on('ref_sumber_biaya');
                $table->foreign('aset_id')->references('id')->on('ref_aset');
                $table->foreign('usulan_id')->references('id')->on('trans_usulan');
            }
        );

        Schema::create('trans_usulan_cc', function (Blueprint $table) {
            $table->unsignedBigInteger('usulan_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('usulan_id')->references('id')->on('trans_usulan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sys_users');

            $table->primary(['usulan_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trans_usulan');
        Schema::dropIfExists('trans_usulan_details');
        Schema::dropIfExists('trans_usulan_cc');
    }
}
