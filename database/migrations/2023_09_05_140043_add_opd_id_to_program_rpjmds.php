<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpdIdToProgramRpjmds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('program_rpjmds', function (Blueprint $table) {
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
        Schema::table('program_rpjmds', function (Blueprint $table) {
            //
        });
    }
}
