<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotPerubahanTujuanPdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_perubahan_tujuan_pds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tujuan_pd_id')->nullable();
            $table->foreignId('tujuan_id')->nullable();
            $table->string('kode')->nullable();
            $table->string('deskripsi')->nullable();
            $table->foreignId('opd_id')->nullable();
            $table->string('tahun_perubahan')->nullable();
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
        Schema::dropIfExists('pivot_perubahan_tujuan_pds');
    }
}
