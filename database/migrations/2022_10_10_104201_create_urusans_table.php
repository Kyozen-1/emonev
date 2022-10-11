<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrusansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urusans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable();
            $table->longText('deskripsi')->nullable();
            $table->string('tahun_perubahan')->nullable();
            $table->enum('status_aturan', ['Sebelum Perubahan', 'Sesudah Perubahan'])->nullable();
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
        Schema::dropIfExists('urusans');
    }
}
