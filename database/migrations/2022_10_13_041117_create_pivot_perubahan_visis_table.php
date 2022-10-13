<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotPerubahanVisisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_perubahan_visis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visi_id')->nullable();
            $table->longText('deskripsi')->nullable();
            $table->foreignId('kabupaten_id')->nullable();
            $table->string('tahun_perubahan')->nullable();
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
        Schema::dropIfExists('pivot_perubahan_visis');
    }
}
