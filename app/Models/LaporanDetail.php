<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanDetail extends Model
{
    protected $table = 'm_laporan_detail';
    protected $primaryKey = 'detail_id';
    
    protected $fillable = [
        'laporan_id',
        'fasilitas_id',
        'foto_bukti',
        'deskripsi'
    ];

    // Relasi dengan laporan
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(LaporanModel::class, 'laporan_id', 'laporan_id');
    }

    // Relasi dengan fasilitas
    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(FasilitasModel::class, 'fasilitas_id', 'fasilitas_id');
    }
}