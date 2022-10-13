<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramRpjmdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_rpjmds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable();
            $table->foreignId('pivot_sasaran_indikator_id')->nullable();
            $table->enum('status_program', ['Program Prioritas', 'Program Pendukung'])->nullable();
            $table->string('pagu')->nullable();
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
        Schema::dropIfExists('program_rpjmds');
    }
}
