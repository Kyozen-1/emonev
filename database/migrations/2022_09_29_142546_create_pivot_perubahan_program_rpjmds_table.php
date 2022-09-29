<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotPerubahanProgramRpjmdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_perubahan_program_rpjmds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_rpjmd_id')->nullable();
            $table->foreignId('program_id')->nullable();
            $table->foreignId('sasaran_id')->nullable();
            $table->string('pagu')->nullable();
            $table->longText('deskripsi')->nullable();
            $table->enum('status_program', ['program_prioritas', 'program_pendukung']);
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
        Schema::dropIfExists('pivot_perubahan_program_rpjmds');
    }
}
