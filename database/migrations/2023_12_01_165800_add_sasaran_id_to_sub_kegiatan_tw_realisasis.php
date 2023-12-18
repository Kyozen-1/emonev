<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSasaranIdToSubKegiatanTwRealisasis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_kegiatan_tw_realisasis', function (Blueprint $table) {
            $table->foreignId('sasaran_id')->nullable();
            $table->foreignId('program_id')->nullable();
            $table->foreignId('kegiatan_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_kegiatan_tw_realisasis', function (Blueprint $table) {
            //
        });
    }
}
