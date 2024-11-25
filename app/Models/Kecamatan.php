<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'kecamatan';

    protected $fillable = [
        'kodekecamatan',
        'namakecamatan',
        'kodekabupaten',
        'kodeprovinsi',
        'userinput',
        'tglinput',
        'userupdate',
        'tglupdate'
    ];

    // Relationships
    public function kelurahans()
    {
        return $this->hasMany(Kelurahan::class, 'kodekecamatan', 'kodekecamatan');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kodekabupaten', 'kodekabupaten');
    }
}
