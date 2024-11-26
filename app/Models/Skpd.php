<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skpd extends Model
{
    protected $table = 'skpd';
    protected $primaryKey = 'nosptpd';
    public $timestamps = false;

    protected $fillable = [
        'tglsptpd',
        'tglskpd',
        'kodepajak',
        'kodeobjek',
        'nop',
        'npwpd',
        'masapajak',
        'nilaiomzet',
        'persenpajak',
        'nilaipajak',
        'tglinput',
        'tglupdate',
        'userinput',
        'userupdate',
    ];

    // Relationship with Sptpd
    public function sptpd()
    {
        return $this->belongsTo(Sptpd::class, 'nosptpd', 'nosptpd');
    }

    // Relationship with WajibPajak
    public function wajibPajak()
    {
        return $this->belongsTo(WajibPajak::class, 'npwpd', 'npwpd');
    }


    public function sspd()
    {
        return $this->hasOne(Sspd::class, 'nosptpd', 'nosptpd');
    }

    public function objectPajak()
    {
        return $this->belongsTo(DataObjekPajak::class, 'nop', 'nop');
    }
}
