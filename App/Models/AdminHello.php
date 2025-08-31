<?php

namespace Jiny\Admin2\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminHello extends Model
{
    use HasFactory;

    protected $table = 'admin_hello';

    protected $fillable = [
        'name',
        'message',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}