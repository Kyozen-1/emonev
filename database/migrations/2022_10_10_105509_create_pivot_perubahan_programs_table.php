<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotPerubahanProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_perubahan_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable();
            $table->foreignId('urusan_id')->nullable();
            $table->string('kode')->nullable();
            $table->longText('deskripsi')->nullable();
            $table->string('tahun_perubahan')->nullable();
            $table->foreignId('kabupaten_id')->nullable();
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
        Schema::dropIfExists('pivot_perubahan_programs');
    }
}
