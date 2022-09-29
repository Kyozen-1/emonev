<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetRpPertahunTujuansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_rp_pertahun_tujuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pivot_tujuan_indikator_id')->nullable();
            $table->string('target')->nullable();
            $table->string('rp')->nullable();
            $table->string('tahun')->nullable();
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
        Schema::dropIfExists('target_rp_pertahun_tujuans');
    }
}
