<?php

namespace Jiny\Admin\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'isAdmin',
        'utype',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'isAdmin' => 'boolean',
        ];
    }

    public function userType()
    {
        return $this->belongsTo(AdminUsertype::class, 'utype', 'code');
    }

    public function isAdmin()
    {
        return $this->isAdmin === true;
    }

    public function hasRole($role)
    {
        return $this->utype === $role;
    }

    public function getDisplayTypeAttribute()
    {
        if ($this->userType) {
            return $this->userType->name;
        }
        return $this->utype ?: 'User';
    }

    public function scopeAdmins($query)
    {
        return $query->where('isAdmin', true);
    }

    public function scopeRegularUsers($query)
    {
        return $query->where('isAdmin', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('utype', $type);
    }
}