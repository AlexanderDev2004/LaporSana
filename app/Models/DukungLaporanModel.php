<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DukungLaporanModel extends Model
{
    use HasFactory;
    protected $table = 'dukungan_laporan';
    protected $guarded = ['dukungan_id'];
}
