<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRkpdTahunPembangunanSubKegiatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rkpd_tahun_pembangunan_sub_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rkpd_tahun_pembangunan_kegiatan_id')->nullable();
            $table->foreignId('sub_kegiatan_id')->nullable();
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
        Schema::dropIfExists('rkpd_tahun_pembangunan_sub_kegiatans');
    }
}
