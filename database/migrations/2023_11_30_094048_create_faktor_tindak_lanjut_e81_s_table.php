<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaktorTindakLanjutE81STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faktor_tindak_lanjut_e81_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_periode_id')->nullable();
            $table->foreign('tahun_periode_id')->references('id')->on('tahun_periodes')->onDelete('cascade');
            $table->string('tahun')->nullable();
            $table->longText('faktor_pendorong')->nullable();
            $table->longText('faktor_penghambat')->nullable();
            $table->longText('tindak_lanjut_triwulan')->nullable();
            $table->longText('tindak_lanjut_renja')->nullable();
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
        Schema::dropIfExists('faktor_tindak_lanjut_e81_s');
    }
}
