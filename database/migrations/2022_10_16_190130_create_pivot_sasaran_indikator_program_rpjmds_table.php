<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotSasaranIndikatorProgramRpjmdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_sasaran_indikator_program_rpjmds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_rpjmd_id')->nullable();
            $table->foreignId('sasaran_indikator_id')->nullable();
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
        Schema::dropIfExists('pivot_sasaran_indikator_program_rpjmds');
    }
}
