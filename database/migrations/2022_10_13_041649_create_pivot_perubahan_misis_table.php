<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotPerubahanMisisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_perubahan_misis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('misi_id')->nullable();
            $table->foreignId('kabupaten_id')->nullable();
            $table->foreignId('visi_id')->nullable();
            $table->string('kode')->nullable();
            $table->longText('deskripsi')->nullable();
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
        Schema::dropIfExists('pivot_perubahan_misis');
    }
}
