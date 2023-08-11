<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusLockToSasaranPdIndikatorKinerjas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sasaran_pd_indikator_kinerjas', function (Blueprint $table) {
            $table->enum('status_lock', [0, 1]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sasaran_pd_indikator_kinerjas', function (Blueprint $table) {
            //
        });
    }
}
