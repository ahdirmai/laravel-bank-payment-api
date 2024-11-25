<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sspd extends Model
{
    protected $table = 'sspd';
    protected $primaryKey = 'nosptpd';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'char';
    protected $fillable = [
        'nosptpd',
        'tglbayar',
        'jumlahbayar',
        'modebayar',
        'kasir',
        'userinput',
        'tglinput',
    ];

    // Relationship with Sptpd
    public function sptpd()
    {
        return $this->belongsTo(Sptpd::class, 'nosptpd', 'nosptpd');
    }
}
