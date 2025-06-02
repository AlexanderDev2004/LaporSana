<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanModel extends Model
{
    use HasFactory;
    protected $table = 'm_laporan';
    protected $primaryKey = 'laporan_id';
    protected $fillable = [
        'user_id',
        'fasilitas_id',
        'status_id',
        'tanggal_lapor',
        'foto_bukti',
        'deskripsi',
        'jumlah_pelapor',
    ];
    public function user()
    {
        return $this->hasMany(UserModel::class, 'user_id', 'id');
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
