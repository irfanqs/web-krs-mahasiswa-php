<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jadwal_kuliah', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->unsignedBigInteger('id_matkul');
            $table->string('id_dosen', 15);
            $table->unsignedBigInteger('id_semester');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruang', 30);
            $table->smallInteger('kuota');
            $table->timestamps();

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_dosen')->references('nidn')->on('dosen')->onDelete('cascade');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kuliah');
    }
};
