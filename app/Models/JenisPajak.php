<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPajak extends Model
{
    use HasFactory;

    protected $table = 'jenispajak';
    protected $primaryKey = 'kodepajak';

    protected $fillable = [
        'namapajak',
        'tglinput',
        'userinput',
        'tglupdate',
        'userupdate',
        'modedata',
        'metode'
    ];

    // Relationships
    public function dataObjekPajak()
    {
        return $this->hasMany(DataObjekPajak::class, 'kodepajak', 'kodepajak');
    }
}
