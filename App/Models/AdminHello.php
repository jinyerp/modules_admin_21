<?php

namespace Jiny\Admin\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminHello extends Model
{
    use HasFactory;

    protected $table = 'admin_hellos';

    protected $fillable = [
        'enable',
        'title',
        'description',
        'pos',
        'depth',
        'ref'
    ];

    protected $casts = [
        'enable' => 'boolean'
    ];
}