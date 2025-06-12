<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasDetailModel extends Model
{
    protected $table = 'm_tugas_detail';
    protected $primaryKey = 'detail_id';

    protected $fillable = [
        'tugas_id',
        'fasilitas_id',
        'tingkat_kerusakan',
        'biaya_perbaikan',
        'tugas_image',
        'deskripsi',
    ];

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(TugasModel::class, 'tugas_id', 'tugas_id');
    }

    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(FasilitasModel::class, 'fasilitas_id', 'fasilitas_id');
    }

    
}
