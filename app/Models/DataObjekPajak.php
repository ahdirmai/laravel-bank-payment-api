<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataObjekPajak extends Model
{
    use HasFactory;

    protected $table = 'dataobjekpajak';
    protected $primaryKey = 'nop';

    protected $fillable = [
        'namaobjekpajak',
        'alamat',
        'kodedesa',
        'kodekec',
        'notlp',
        'luastempat',
        'statusmilik',
        'kodepajak',
        'kodeobjekpajak',
        'npwpd',
        'tmtoperasi',
        'noppbb',
        'tglinput',
        'userinput',
        'tglupdate',
        'userupdate',
        'fotoobjekpajak'
    ];

    // Relationships
    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'kodepajak', 'kodepajak');
    }

    public function objekPajak()
    {
        return $this->belongsTo(ObjekPajak::class, 'kodeobjekpajak', 'kodeobjek');
    }

    public function wajibPajak()
    {
        return $this->belongsTo(WajibPajak::class, 'npwpd', 'npwpd');
    }
}
