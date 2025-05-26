<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserModel extends Authenticatable
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

    //ambil nama role
    public function getRoleName(): string
    {
        return $this->role->roles_nama;
    }

    //cek apakah user memiliki role tertentu
    public function hasRole(string $role): bool
    {
        return $this->role->roles_kode === $role;
    }

    public function getRole()
    {
        return $this->role->roles_kode;
    }


    public function getNamaAttribute()
    {
        return match ($this->role->roles_kode) {
            'MHS' => $this->mahasiswa?->nama,
            'DSN' => $this->dosen?->nama,
            'ADM' => $this->admin?->nama,
            default => null,
        };
    }
}
