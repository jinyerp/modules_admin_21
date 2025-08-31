<?php

namespace Jiny\Admin2\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminTest extends Model
{
    use HasFactory;

    protected $table = 'admin_tests';

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