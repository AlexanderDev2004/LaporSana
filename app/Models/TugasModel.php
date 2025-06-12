<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasModel extends Model
{
    protected $table = 'm_tugas';
    protected $primaryKey = 'tugas_id';

    protected $fillable = [
        'user_id',
        'status_id',
        'tugas_jenis',
        'tugas_mulai',
        'tugas_selesai'
    ];

    // Relasi dengan detail tugas
    public function details(): HasMany
    {
        return $this->hasMany(TugasDetailModel::class, 'tugas_id', 'tugas_id');
    }

    // Relasi dengan user (teknisi)
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    // Relasi dengan status
    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusModel::class, 'status_id', 'status_id');
    }
}
