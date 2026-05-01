<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Krs extends Model
{
    protected $table = 'krs';
    protected $primaryKey = 'id_krs';

    protected $fillable = ['id_mahasiswa', 'id_jadwal', 'tanggal_ambil'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'nim');
    }

    public function jadwalKuliah()
    {
        return $this->belongsTo(JadwalKuliah::class, 'id_jadwal', 'id_jadwal');
    }

    public function nilai()
    {
        return $this->hasOne(Nilai::class, 'id_krs', 'id_krs');
    }
}
