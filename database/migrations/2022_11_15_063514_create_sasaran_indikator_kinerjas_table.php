<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSasaranIndikatorKinerjasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sasaran_indikator_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sasaran_id')->nullable();
            $table->string('deskripsi')->nullable();
            $table->string('satuan')->nullable();
            $table->integer('kondisi_target_kinerja_awal')->nullable();
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
        Schema::dropIfExists('sasaran_indikator_kinerjas');
    }
}
