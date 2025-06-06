<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusModel extends Model
{
    use HasFactory;
    protected $table = 'm_status';
    protected $primaryKey = 'status_id';
    protected $fillable = [
        'status_nama',
    ];
}
