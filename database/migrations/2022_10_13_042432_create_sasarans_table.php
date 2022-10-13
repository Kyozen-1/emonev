<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSasaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sasarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tujuan_id')->nullable();
            $table->string('kode')->nullable();
            $table->longText('deskripsi')->nullable();
            $table->foreignId('kabupaten_id')->nullable();
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
        Schema::dropIfExists('sasarans');
    }
}
