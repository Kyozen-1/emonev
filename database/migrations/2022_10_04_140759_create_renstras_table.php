<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRenstrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('renstras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('misi_id')->nullable();
            $table->foreignId('tujuan_id')->nullable();
            $table->foreignId('sasaran_id')->nullable();
            $table->foreignId('program_id')->nullable();
            $table->foreignId('opd_id')->nullable();
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
        Schema::dropIfExists('renstras');
    }
}
