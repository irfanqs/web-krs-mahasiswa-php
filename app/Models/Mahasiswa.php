<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Mahasiswa extends Authenticatable
{
    use Notifiable;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim', 'nama', 'email', 'password', 'angkatan',
        'program_studi', 'status', 'foto',
    ];

    protected $hidden = ['password'];

    public function krs()
    {
        return $this->hasMany(Krs::class, 'id_mahasiswa', 'nim');
    }
}
