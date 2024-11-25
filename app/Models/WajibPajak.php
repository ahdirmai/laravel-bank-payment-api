<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WajibPajak extends Model
{
    protected $table = 'wajibpajak';
    protected $primaryKey = 'npwpd';
    public $timestamps = false;

    protected $fillable = [
        'namawpd',
        'nik',
        'namalkp',
        'alamat',
        'kodedesa',
        'kodekec',
        'kodekab',
        'kodeprov',
        'jenisw',
        'npwp',
        'tgldaftar',
        'fotodok',
        'tglinput',
        'userinput',
        'tglupdate',
        'userupdate',
    ];

    // Relationship with Provinsi
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'kodeprov', 'kodeprovinsi');
    }
}
