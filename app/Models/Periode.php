<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    protected $fillable = ['tanggal_mulai', 'tanggal_selesai', 'status'];

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class);
    }
}
