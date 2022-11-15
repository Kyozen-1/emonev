<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTujuanPdIndikatorKinerjasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tujuan_pd_indikator_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tujuan_pd_id')->nullable();
            $table->string('deskripsi')->nullable();
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
        Schema::dropIfExists('tujuan_pd_indikator_kinerjas');
    }
}
