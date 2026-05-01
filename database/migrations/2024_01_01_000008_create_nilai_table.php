<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id('id_nilai');
            $table->unsignedBigInteger('id_krs')->unique();
            $table->decimal('tugas', 5, 2)->nullable();
            $table->decimal('uts', 5, 2)->nullable();
            $table->decimal('uas', 5, 2)->nullable();
            $table->decimal('nilai_angka', 5, 2)->nullable();
            $table->enum('nilai_huruf', ['A', 'B+', 'B', 'C+', 'C', 'D', 'E'])->nullable();
            $table->tinyInteger('status_kunci')->default(0);
            $table->timestamps();

            $table->foreign('id_krs')->references('id_krs')->on('krs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
