<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotPerubahanSasaranPdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_perubahan_sasaran_pds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sasaran_pd_id')->nullable();
            $table->foreignId('sasaran_id')->nullable();
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
        Schema::dropIfExists('pivot_perubahan_sasaran_pds');
    }
}
