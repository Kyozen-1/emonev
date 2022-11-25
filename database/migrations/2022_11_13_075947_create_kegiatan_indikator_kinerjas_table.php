<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKegiatanIndikatorKinerjasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kegiatan_indikator_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->nullable();
            $table->string('deskripsi')->nullable();
            $table->string('satuan')->nullable();
            $table->string('kondisi_target_kinerja_awal')->nullable();
            $table->string('kondisi_target_anggaran_awal')->nullable();
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
        Schema::dropIfExists('kegiatan_indikator_kinerjas');
    }
}
