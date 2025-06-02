<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasModel extends Model
{
    use HasFactory;
    protected $table = 'm_tugas';
    protected $primaryKey = 'tugas_id';
    protected $fillable = [
        'user_id',
        'fasilitas_id',
        'status_id',
        'tugas_jenis',
        'tugas_mulai',
        'tugas_selesai',
        'tugas_image',
        'deskripsi',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function fasilitas()
    {
        return $this->belongsTo(FasilitasModel::class, 'fasilitas_id', 'fasilitas_id');
    }
    public function status()
    {
        return $this->belongsTo(StatusModel::class, 'status_id', 'status_id');
    }
}
