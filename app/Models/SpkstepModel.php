<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkstepModel extends Model
{
    protected $table = 't_spk_steps';
    protected $primaryKey = 'fasilitas_id';

    protected $fillable = [
        'fasilitas_id',
        'metode',
        'step',
        'urutan',
        'hasil',
    ];

    protected $casts = [
        'hasil' => 'array',
    ];

    // Relasi ke Fasilitas
    public function fasilitas()
    {
        return $this->belongsTo(FasilitasModel::class, 'fasilitas_id', 'fasilitas_id');
    }


    // Scope untuk memfilter berdasarkan metode
    public function scopeMetode($query, $metode)
    {
        return $query->where('metode', $metode);
    }

    // Scope untuk urutkan step
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}
