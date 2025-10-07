<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserModel extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    public $incrementing = true;

    protected $fillable = [
        'roles_id',
        'username',
        'name',
        'password',
        'NIM',
        'NIP',
        'avatar'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed', // otomatis hash password
    ];

    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'roles_id', 'roles_id');
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($image) => url('/storage/posts/' . $image),
        );
    }

    public function getRoleName(): string
    {
        return $this->role->roles_nama;
    }

    public function hasRole(string $role): bool
    {
        return $this->role->roles_kode === $role;
    }

    public function getRole()
    {
        return $this->role->roles_kode;
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function username()
    {
        return 'username';
    }
}
