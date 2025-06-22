<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekomperbaikanModel extends Model
{
    use HasFactory;
    protected $table = 't_rekomperbaikan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'fasilitas_id',
        'rank',
        'score_ranking'
    ];

    public function fasilitas()
    {
        return $this->belongsTo(FasilitasModel::class, 'fasilitas_id', 'fasilitas_id');
    }



}
