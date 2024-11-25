<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $table = 'provinsi';
    protected $primaryKey = 'kodeprovinsi';
    public $timestamps = false;

    protected $fillable = [
        'namaprovinsi',
        'userinput',
        'tglinput',
        'userupdate',
        'tglupdate',
    ];

    // Relationship with Kabupaten
    public function kabupaten()
    {
        return $this->hasMany(Kabupaten::class, 'kodeprovinsi', 'kodeprovinsi');
    }
}
