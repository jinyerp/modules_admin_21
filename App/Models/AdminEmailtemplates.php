<?php

namespace Jiny\Admin\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminEmailtemplates extends Model
{
    use HasFactory;

    protected $table = 'admin_email_templates';

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'variables',
        'type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'variables' => 'array'
    ];

    /**
     * Get parsed body with replaced variables
     */
    public function getParsedBody(array $data = []): string
    {
        $body = $this->body;
        
        foreach ($data as $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        
        return $body;
    }

    /**
     * Get parsed subject with replaced variables
     */
    public function getParsedSubject(array $data = []): string
    {
        $subject = $this->subject;
        
        foreach ($data as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
        }
        
        return $subject;
    }
}