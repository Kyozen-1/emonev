<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotOpdRentraKegiatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_opd_rentra_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rentra_kegiatan_id')->nullable();
            $table->foreignId('opd_id')->nullable();
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
        Schema::dropIfExists('pivot_opd_rentra_kegiatans');
    }
}
