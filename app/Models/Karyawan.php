<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawans';

    protected $fillable = [
        'periode_id',
        'nik',
        'nama',
        'jabatan',
        'gaji_pokok',
        'lembur',
        'pinjaman',
    ];

    /**
     * Relasi ke tabel Periode
     */
    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}