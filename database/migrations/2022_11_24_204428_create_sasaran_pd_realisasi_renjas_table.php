<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSasaranPdRealisasiRenjasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sasaran_pd_realisasi_renjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sasaran_pd_target_satuan_rp_realisasi_id')->nullable();
            $table->string('realisasi')->nullable();
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
        Schema::dropIfExists('sasaran_pd_realisasi_renjas');
    }
}
