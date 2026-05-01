<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';
    protected $primaryKey = 'id_matkul';

    protected $fillable = ['kode_matkul', 'nama_matkul', 'sks', 'semester', 'jenis'];

    public function jadwalKuliah()
    {
        return $this->hasMany(JadwalKuliah::class, 'id_matkul', 'id_matkul');
    }
}
