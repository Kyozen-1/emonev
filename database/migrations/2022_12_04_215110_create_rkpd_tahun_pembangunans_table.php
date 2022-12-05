<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRkpdTahunPembangunansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rkpd_tahun_pembangunans', function (Blueprint $table) {
            $table->id();
            $table->text('deskripsi')->nullable();
            $table->string('tahun')->nullable();
            $table->foreignId('kabupate_id')->nullable();
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
        Schema::dropIfExists('rkpd_tahun_pembangunans');
    }
}
