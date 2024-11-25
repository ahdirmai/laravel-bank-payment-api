<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    use HasFactory;

    protected $table = 'kelurahan';

    protected $fillable = [
        'kodekelurahan',
        'namakelurahan',
        'kodekecamatan',
        'kodekabupaten',
        'kodeprovinsi',
        'jeniskelurahan',
        'tglinput',
        'userinput',
        'tglupdate',
        'userupdate'
    ];

    // Relationships
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kodekecamatan', 'kodekecamatan');
    }
}
