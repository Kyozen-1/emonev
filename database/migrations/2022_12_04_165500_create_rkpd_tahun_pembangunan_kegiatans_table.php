<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRkpdTahunPembangunanKegiatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rkpd_tahun_pembangunan_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rkpd_tahun_pembangunan_program_id')->nullable();
            $table->foreignId('kegiatan_id')->nullable();
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
        Schema::dropIfExists('rkpd_tahun_pembangunan_kegiatans');
    }
}
