<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $table = 'semester';
    protected $primaryKey = 'id_semester';

    protected $fillable = ['tahun_ajaran', 'tingkatan_semester', 'status'];

    public function jadwalKuliah()
    {
        return $this->hasMany(JadwalKuliah::class, 'id_semester', 'id_semester');
    }

    public static function aktif()
    {
        return static::where('status', 'aktif')->first();
    }
}
