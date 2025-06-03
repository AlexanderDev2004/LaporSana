<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LantaiModel extends Model
{
    use HasFactory;
    
    protected $table = 'm_lantai';
    protected $primaryKey = 'lantai_id';

    protected $fillable = [
        'lantai_kode',
        'lantai_nama',
        'created_at',
        'updated_at'
    ];
}
