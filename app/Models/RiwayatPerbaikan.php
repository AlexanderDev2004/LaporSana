<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPerbaikan extends Model
{
    protected $table = 'riwayat_perbaikan';
    protected $primaryKey = 'riwayat_id';
    protected $fillable = ['tugas_id', 'rating', 'ulasan'];

    public function tugas()
{
    return $this->belongsTo(TugasModel::class, 'tugas_id', 'tugas_id');
}
}
