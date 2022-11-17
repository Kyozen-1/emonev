<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKegiatanTwRealisasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kegiatan_tw_realisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_target_satuan_rp_realisasi_id')->nullable();
            $table->foreignId('tw_id')->nullable();
            $table->string('realisasi')->nullable();
            $table->string('realisasi_rp')->nullable();
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
        Schema::dropIfExists('kegiatan_tw_realisasis');
    }
}
