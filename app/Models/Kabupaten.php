<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    protected $table = 'kabupaten';

    protected $fillable = [
        'kodeprovinsi',
        'kodekabupaten',
        'namakabupaten',
        'jeniskabupaten',
        'userinput',
        'tglinput',
        'userupdate',
        'tglupdate'
    ];

    // Relationships
    public function kecamatans()
    {
        return $this->hasMany(Kecamatan::class, 'kodekabupaten', 'kodekabupaten');
    }
}
