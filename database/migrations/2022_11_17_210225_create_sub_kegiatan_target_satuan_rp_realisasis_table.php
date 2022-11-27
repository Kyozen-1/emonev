<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubKegiatanTargetSatuanRpRealisasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_kegiatan_target_satuan_rp_realisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_sub_kegiatan_indikator_kinerja_id')->nullable();
            $table->string('target')->nullable();
            $table->string('target_anggaran_renja_awal')->nullable();
            $table->string('target_anggaran_renja_perubahan')->nullable();
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
        Schema::dropIfExists('sub_kegiatan_target_satuan_rp_realisasis');
    }
}
