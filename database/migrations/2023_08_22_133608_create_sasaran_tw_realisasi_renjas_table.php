<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSasaranTwRealisasiRenjasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sasaran_tw_realisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sasaran_target_satuan_rp_realisasi_id')->nullable();
            $table->foreignId('tw_id')->nullable();
            $table->foreign('tw_id')->references('id')->on('master_tws')->onDelete('cascade');
            $table->string('realisasi')->nullable();
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
        Schema::dropIfExists('sasaran_tw_realisasi_renjas');
    }
}
