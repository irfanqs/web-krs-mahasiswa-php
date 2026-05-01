<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('semester', function (Blueprint $table) {
            $table->id('id_semester');
            $table->string('tahun_ajaran', 9);
            $table->enum('tingkatan_semester', ['ganjil', 'genap']);
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
            $table->timestamps();
            $table->unique(['tahun_ajaran', 'tingkatan_semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester');
    }
};
