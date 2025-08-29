<?php

namespace Jiny\Admin2\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    use HasFactory;

    protected $table = 'admin_users';

    protected $fillable = [
        'enable',
        'name',
        'email',
    ];

    protected $casts = [
        'enable' => 'boolean',
    ];
}