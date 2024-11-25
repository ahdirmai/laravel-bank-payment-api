<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjekPajak extends Model
{
    protected $table = 'objekpajak';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'kodeobjek',
        'kodepajak',
        'namaobjek',
        'tglinput',
        'userinput',
        'tglupdate',
        'userupdate',
        'modedata',
        'persenpajak',
    ];

    // Relationship with JenisPajak
    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'kodepajak', 'kodepajak');
    }
}
