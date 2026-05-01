<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Dosen extends Authenticatable
{
    use Notifiable;

    protected $table = 'dosen';
    protected $primaryKey = 'nidn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['nidn', 'nama', 'email', 'password', 'jurusan'];

    protected $hidden = ['password'];

    public function jadwalKuliah()
    {
        return $this->hasMany(JadwalKuliah::class, 'id_dosen', 'nidn');
    }
}
