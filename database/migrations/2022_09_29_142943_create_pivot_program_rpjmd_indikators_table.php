<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotProgramRpjmdIndikatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_program_rpjmd_indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_rpjmd_id')->nullable();
            $table->longText('indikator')->nullable();
            $table->string('target')->nullable();
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
        Schema::dropIfExists('pivot_program_rpjmd_indikators');
    }
}
