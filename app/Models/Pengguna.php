<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    protected $table = 'pengguna';
    protected $primaryKey = 'nik';
    public $timestamps = false;

    protected $fillable = [
        'namal',
        'npwpd',
        'sandi',
        'level',
        'nohp',
        'kodelevel',
        'fotopengguna',
        'statusaktif',
        'userinput',
        'statuslogin',
        'tglinput',
        'userupdate',
        'tglupdate',
    ];

    // Relationship with WajibPajak
    public function wajibPajak()
    {
        return $this->hasMany(WajibPajak::class, 'npwpd', 'npwpd');
    }
}
