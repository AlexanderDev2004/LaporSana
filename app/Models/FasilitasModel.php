<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FasilitasModel extends Model
{
    use HasFactory;
    protected $table = 'm_fasilitas';
    protected $primaryKey = 'fasilitas_id';
    protected $fillable = [
        'ruangan_id',
        'fasilitas_kode',
        'fasilitas_nama',
    ];
    public function ruangan()
    {
        return $this->belongsTo(RuanganModel::class, 'ruangan_id', 'ruangan_id');
    }
}
