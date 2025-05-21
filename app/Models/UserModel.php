<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    use HasFactory;

    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $fillable = [
        'roles_id',
        'username',
        'nama',
        'password',
        'NIM',
        'NIP',
        'avatar'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'password' => 'hashed', //casting pw agar dihash otomatis
    ];

    // protected function image(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($image) => url('/storage/posts/' . $image),
    //     );
    // }

    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'roles_id', 'roles_id');
    }
}
