<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTahunPeriodeIdToSubKegiatans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->foreignId('tahun_periode_id')->nullable();
            $table->foreign('tahun_periode_id')->references('id')->on('tahun_periodes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            //
        });
    }
}
