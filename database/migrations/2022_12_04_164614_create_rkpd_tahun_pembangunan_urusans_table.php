<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRkpdTahunPembangunanUrusansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rkpd_tahun_pembangunan_urusans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rkpd_opd_tahun_pembangunan_id')->nullable();
            $table->foreignId('urusan_id')->nullable();
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
        Schema::dropIfExists('rkpd_tahun_pembangunan_urusans');
    }
}
