<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTujuanPdRealisasiRenjasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tujuan_pd_realisasi_renjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tujuan_pd_target_satuan_rp_realisasi_id')->nullable();
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
        Schema::dropIfExists('tujuan_pd_realisasi_renjas');
    }
}
