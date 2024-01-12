<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpdIdToFaktorTindakLanjutE80S extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faktor_tindak_lanjut_e80_s', function (Blueprint $table) {
            $table->foreignId('opd_id')->nullable();
            $table->foreign('opd_id')->references('id')->on('master_opds')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faktor_tindak_lanjut_e_80_s', function (Blueprint $table) {
            //
        });
    }
}
