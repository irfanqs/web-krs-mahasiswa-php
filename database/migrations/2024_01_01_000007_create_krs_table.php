<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('krs', function (Blueprint $table) {
            $table->id('id_krs');
            $table->string('id_mahasiswa', 15);
            $table->unsignedBigInteger('id_jadwal');
            $table->dateTime('tanggal_ambil')->useCurrent();
            $table->timestamps();

            $table->unique(['id_mahasiswa', 'id_jadwal']);
            $table->foreign('id_mahasiswa')->references('nim')->on('mahasiswa')->onDelete('cascade');
            $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal_kuliah')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};
