<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationAset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        Schema::create('ref_location_aset', function (Blueprint $table) {
            $table->id();
            // Kolom-kolom lain
            $table->string("location_code")->unique();
            $table->string("name")->unique();
            $table->enum("floor_position", ['1','2','3','4','5','6'])->default('1');
            $table->unsignedBigInteger("departemen_id");
            $table->unsignedBigInteger("space_manager_id");
            $table->foreign('departemen_id')->references('id')->on('sys_org_structs');
            $table->foreign('space_manager_id')->references('id')->on('sys_users');

            // ...Tambahkan kolom-kolom lain sesuai kebutuhan
            $table->timestamps();
            // $table->foreign('departemen_id')->references('id')->on('sys_org_structs');
            // $table->foreign('space_manager_id')->references('id')->on('sys_users');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_location_aset');
    }
}
