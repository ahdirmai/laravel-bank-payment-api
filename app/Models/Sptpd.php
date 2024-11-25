<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sptpd extends Model
{
    protected $table = 'sptpd';
    protected $primaryKey = 'nosptpd';
    public $timestamps = false;

    protected $fillable = [
        'npwpd',
        'nop',
        'kodepajak',
        'kodeobjek',
        'tahun',
        'bulan',
        'uraian',
        'tgllapor',
        'tgltempo',
        'userinput',
        'tglinput',
    ];

    // Relationship with WajibPajak
    public function wajibPajak()
    {
        return $this->belongsTo(WajibPajak::class, 'npwpd', 'npwpd');
    }

    
}
