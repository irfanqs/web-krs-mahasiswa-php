<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id('id_matkul');
            $table->string('kode_matkul', 10)->unique();
            $table->string('nama_matkul', 100);
            $table->tinyInteger('sks');
            $table->tinyInteger('semester');
            $table->enum('jenis', ['wajib', 'pilihan']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};
