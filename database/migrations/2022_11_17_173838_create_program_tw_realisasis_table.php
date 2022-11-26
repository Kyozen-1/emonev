<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramTwRealisasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_tw_realisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_target_satuan_rp_realisasi_id')->nullable();
            $table->foreignId('tw_id')->nullable();
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
        Schema::dropIfExists('program_tw_realisasis');
    }
}
