<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpdSubKegiatanIndikatorKinerjasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opd_sub_kegiatan_indikator_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_kegiatan_indikator_kinerja_id')->nullable();
            $table->foreignId('opd_id')->nullable();
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
        Schema::dropIfExists('opd_sub_kegiatan_indikator_kinerjas');
    }
}
