<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotSasaranIndikatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_sasaran_indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sasaran_id')->nullable();
            $table->longText('indikator')->nullable();
            $table->string('target')->nullalbe();
            $table->string('satuan')->nullable();
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
        Schema::dropIfExists('pivot_sasaran_indikators');
    }
}
