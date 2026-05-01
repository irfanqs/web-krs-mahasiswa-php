<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKuliah extends Model
{
    protected $table = 'jadwal_kuliah';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'id_matkul', 'id_dosen', 'id_semester',
        'hari', 'jam_mulai', 'jam_selesai', 'ruang', 'kuota',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'id_matkul', 'id_matkul');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'nidn');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }

    public function krs()
    {
        return $this->hasMany(Krs::class, 'id_jadwal', 'id_jadwal');
    }
}
