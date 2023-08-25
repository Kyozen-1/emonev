<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTargetAnggaranPerubahanToKegiatanTargetSatuanRpRealisasis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kegiatan_target_satuan_rp_realisasis', function (Blueprint $table) {
            $table->string('target_anggaran_perubahan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kegiatan_target_satuan_rp_realisasis', function (Blueprint $table) {
            //
        });
    }
}
