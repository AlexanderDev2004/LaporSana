<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanModel extends Model
{
    protected $table = 'm_laporan';
    protected $primaryKey = 'laporan_id';

    protected $fillable = [
        'user_id',
        'status_id',
        'tanggal_lapor',
        'jumlah_pelapor'
    ];

    // Relasi dengan detail laporan
    public function details(): HasMany
    {
        return $this->hasMany(LaporanDetail::class, 'laporan_id', 'laporan_id');
    }

    // Relasi dengan user
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    // Relasi dengan status
    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusModel::class, 'status_id', 'status_id');
    }

    public function tugas()
    {
        return $this->hasOne(TugasModel::class, 'laporan_id', 'laporan_id');
    }
}
