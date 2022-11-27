<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubKegiatanIndikatorKinerjasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_kegiatan_indikator_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_kegiatan_id')->nulable();
            $table->string('deskripsi')->nullable();
            $table->string('satuan')->nullable();
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
        Schema::dropIfExists('sub_kegiatan_indikator_kinerjas');
    }
}
