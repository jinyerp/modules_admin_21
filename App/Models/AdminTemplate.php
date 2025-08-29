<?php

namespace Jiny\Admin2\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminTemplate extends Model
{
    use HasFactory;

    protected $table = 'admin_templates';

    protected $fillable = [
        'enable',
        'name',
        'slug',
        'description',
        'category',
        'version',
        'author',
        'settings'
    ];

    protected $casts = [
        'enable' => 'boolean',
        'settings' => 'array'
    ];

    protected static function newFactory()
    {
        return \Jiny\Admin2\Database\Factories\AdminTemplateFactory::new();
    }
}