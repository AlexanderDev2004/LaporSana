<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DukungLaporanModel extends Model
{
    use HasFactory;
    protected $table = 'dukungan_laporan';
    protected $guarded = ['dukungan_id'];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
