<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterSkalaNilaiPerangkatKinerjasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_skala_nilai_perangkat_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->integer('terkecil')->nullable();
            $table->integer('terbesar')->nullable();
            $table->string('kriteria')->nullable();
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
        Schema::dropIfExists('master_skala_nilai_perangkat_kinerjas');
    }
}
