<?php

namespace Jiny\Admin\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\AdminEmailtemplatesFactory;
use Illuminate\Database\Eloquent\Model;

class AdminEmailtemplates extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Database\Factories\AdminEmailtemplatesFactory
     */
    protected static function newFactory()
    {
        return AdminEmailtemplatesFactory::new();
    }

    protected $table = 'admin_email_templates';

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'variables',
        'type',
        'category',
        'is_active',
        'status',
        'priority',
        'attachments',
        'from_name',
        'from_email',
        'reply_to',
        'cc',
        'bcc',
        'description',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'status' => 'boolean',
        'variables' => 'array',
        'attachments' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'metadata' => 'array'
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