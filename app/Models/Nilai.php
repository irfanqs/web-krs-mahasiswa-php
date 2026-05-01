<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $table = 'nilai';
    protected $primaryKey = 'id_nilai';

    protected $fillable = [
        'id_krs', 'tugas', 'uts', 'uas',
        'nilai_angka', 'nilai_huruf', 'status_kunci',
    ];

    public function krs()
    {
        return $this->belongsTo(Krs::class, 'id_krs', 'id_krs');
    }

    public static function hitungNilaiAngka($tugas, $uts, $uas): float
    {
        return round((0.2 * $tugas) + (0.3 * $uts) + (0.5 * $uas), 2);
    }

    public static function hitungNilaiHuruf(float $angka): string
    {
        if ($angka >= 85) return 'A';
        if ($angka >= 70) return 'B+';
        if ($angka >= 60) return 'B';
        if ($angka >= 55) return 'C+';
        if ($angka >= 50) return 'C';
        if ($angka >= 40) return 'D';
        return 'E';
    }

    public static function bobotHuruf(string $huruf): float
    {
        return match($huruf) {
            'A'  => 4.0,
            'B+' => 3.5,
            'B'  => 3.0,
            'C+' => 2.5,
            'C'  => 2.0,
            'D'  => 1.0,
            default => 0.0,
        };
    }
}
