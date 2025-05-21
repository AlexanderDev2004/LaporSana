<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    use HasFactory;

    protected $table = 'm_roles';
    protected $primaryKey = 'roles_id';
    public $incrementing = true;
    protected $fillable = ['roles_kode', 'roles_nama'];

    public function users()
    {
        return $this->hasMany(User::class, 'roles_id', 'roles_id');
    }
}
