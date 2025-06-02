<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuanganModel extends Model
{
    use HasFactory;
    protected $table = 'm_ruangan';
    protected $primaryKey = 'ruangan_id';
    protected $fillable = [
        'lantai_id',
        'ruangan_kode',
        'ruangan_nama',
    ];
    public function lantai()
    {
        return $this->belongsTo(LantaiModel::class, 'lantai_id', 'lantai_id');
    }
}
