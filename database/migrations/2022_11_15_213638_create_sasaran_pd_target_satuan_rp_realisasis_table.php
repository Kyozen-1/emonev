<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSasaranPdTargetSatuanRpRealisasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sasaran_pd_target_satuan_rp_realisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sasaran_pd_indikator_kinerja_id')->nullable();
            $table->string('target')->nullable();
            $table->string('satuan')->nullable();
            $table->string('realisasi')->nullable();
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
        Schema::dropIfExists('sasaran_pd_target_satuan_rp_realisasis');
    }
}
