<?php

namespace Jiny\Admin\App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

/**
 * 이메일 템플릿 관리 서비스
 * 
 * 템플릿 변수 치환, 미리보기, 렌더링 등의 기능을 제공합니다.
 */
class EmailTemplateService
{
    /**
     * 시스템 기본 변수
     */
    protected $systemVariables = [
        'app_name' => null,
        'app_url' => null,
        'current_year' => null,
        'current_date' => null,
        'current_time' => null,
    ];

    /**
     * 이벤트별 사용 가능한 변수 정의
     */
    protected $eventVariables = [
        'user_registration' => [
            'user_name', 'user_email', 'verification_link', 'expires_at'
        ],
        'password_reset' => [
            'user_name', 'user_email', 'reset_link', 'expires_at', 'ip_address'
        ],
        'login_failed' => [
            'user_email', 'failed_attempts', 'ip_address', 'user_agent', 'attempted_at'
        ],
        'two_fa_enabled' => [
            'user_name', 'user_email', 'enabled_at', 'backup_codes'
        ],
        'ip_blocked' => [
            'ip_address', 'blocked_reason', 'blocked_at', 'blocked_until'
        ],
        'admin_notification' => [
            'admin_name', 'event_type', 'event_description', 'event_data'
        ],
        'test_email' => [
            'recipient_name', 'recipient_email', 'test_message'
        ]
    ];

    public function __construct()
    {
        $this->systemVariables['app_name'] = config('app.name', 'Admin System');
        $this->systemVariables['app_url'] = config('app.url', url('/'));
        $this->systemVariables['current_year'] = date('Y');
        $this->systemVariables['current_date'] = date('Y-m-d');
        $this->systemVariables['current_time'] = date('H:i:s');
    }

    /**
     * 템플릿 슬러그로 템플릿 가져오기
     */
    public function getTemplate(string $slug)
    {
        return DB::table('admin_email_templates')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    /**
     * 템플릿 ID로 템플릿 가져오기
     */
    public function getTemplateById(int $id)
    {
        return DB::table('admin_email_templates')
            ->where('id', $id)
            ->first();
    }

    /**
     * 템플릿 렌더링 (변수 치환)
     */
    public function render($template, array $variables = [])
    {
        if (is_string($template)) {
            $template = $this->getTemplate($template);
        }

        if (!$template) {
            throw new Exception('Template not found');
        }

        // 시스템 변수와 사용자 변수 병합
        $allVariables = array_merge($this->systemVariables, $variables);

        // 제목과 본문 렌더링
        $subject = $this->replaceVariables($template->subject, $allVariables);
        $body = $this->replaceVariables($template->body, $allVariables);

        // Markdown 처리
        if ($template->type === 'markdown') {
            $body = $this->parseMarkdown($body);
        }

        return [
            'subject' => $subject,
            'body' => $this->wrapInLayout($body),
            'template' => $template
        ];
    }

    /**
     * 변수 치환
     */
    protected function replaceVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            // {{variable}} 형식 치환
            $content = str_replace('{{' . $key . '}}', $value ?? '', $content);
            
            // {variable} 형식도 지원
            $content = str_replace('{' . $key . '}', $value ?? '', $content);
        }

        // 치환되지 않은 변수 제거 (옵션)
        $content = preg_replace('/\{\{[^}]+\}\}/', '', $content);
        $content = preg_replace('/\{[^}]+\}/', '', $content);

        return $content;
    }

    /**
     * Markdown을 HTML로 변환
     */
    protected function parseMarkdown(string $markdown): string
    {
        // 간단한 Markdown 파서 (실제로는 league/commonmark 같은 라이브러리 사용 권장)
        $html = $markdown;
        
        // 헤더
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
        
        // 볼드
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        
        // 이탤릭
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        
        // 링크
        $html = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $html);
        
        // 줄바꿈
        $html = nl2br($html);
        
        return $html;
    }

    /**
     * 이메일 레이아웃으로 감싸기
     */
    protected function wrapInLayout(string $content): string
    {
        $layout = '<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{app_name}}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #5a67d8;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert-info {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            color: #1565c0;
        }
        .alert-warning {
            background-color: #fff3e0;
            border-left: 4px solid #ff9800;
            color: #e65100;
        }
        .alert-danger {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
            color: #c62828;
        }
        .alert-success {
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
            color: #2e7d32;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        code {
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: "Courier New", monospace;
            font-size: 14px;
        }
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>' . $this->systemVariables['app_name'] . '</h1>
        </div>
        <div class="content">
            ' . $content . '
        </div>
        <div class="footer">
            <p>&copy; ' . $this->systemVariables['current_year'] . ' ' . $this->systemVariables['app_name'] . '. All rights reserved.</p>
            <p>이 이메일은 자동으로 발송되었습니다. 회신하지 마세요.</p>
        </div>
    </div>
</body>
</html>';

        return $this->replaceVariables($layout, $this->systemVariables);
    }

    /**
     * 템플릿 미리보기 생성
     */
    public function preview($template, array $sampleData = []): array
    {
        // 샘플 데이터가 없으면 기본값 사용
        if (empty($sampleData)) {
            $sampleData = $this->getSampleData($template->slug ?? 'default');
        }

        return $this->render($template, $sampleData);
    }

    /**
     * 이벤트별 샘플 데이터 제공
     */
    public function getSampleData(string $eventType): array
    {
        $samples = [
            'user_registration' => [
                'user_name' => '홍길동',
                'user_email' => 'user@example.com',
                'verification_link' => url('/verify/sample-token'),
                'expires_at' => now()->addHours(24)->format('Y-m-d H:i:s')
            ],
            'password_reset' => [
                'user_name' => '홍길동',
                'user_email' => 'user@example.com',
                'reset_link' => url('/password/reset/sample-token'),
                'expires_at' => now()->addHours(1)->format('Y-m-d H:i:s'),
                'ip_address' => '127.0.0.1'
            ],
            'login_failed' => [
                'user_email' => 'user@example.com',
                'failed_attempts' => 3,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'attempted_at' => now()->format('Y-m-d H:i:s')
            ],
            'two_fa_enabled' => [
                'user_name' => '홍길동',
                'user_email' => 'user@example.com',
                'enabled_at' => now()->format('Y-m-d H:i:s'),
                'backup_codes' => 'ABC123, DEF456, GHI789'
            ],
            'ip_blocked' => [
                'ip_address' => '192.168.1.1',
                'blocked_reason' => '비정상적인 접근 시도',
                'blocked_at' => now()->format('Y-m-d H:i:s'),
                'blocked_until' => now()->addHours(24)->format('Y-m-d H:i:s')
            ],
            'default' => [
                'user_name' => '사용자',
                'user_email' => 'user@example.com',
                'message' => '샘플 메시지입니다.'
            ]
        ];

        return $samples[$eventType] ?? $samples['default'];
    }

    /**
     * 사용 가능한 변수 목록 가져오기
     */
    public function getAvailableVariables(string $eventType = null): array
    {
        $variables = array_keys($this->systemVariables);

        if ($eventType && isset($this->eventVariables[$eventType])) {
            $variables = array_merge($variables, $this->eventVariables[$eventType]);
        }

        return $variables;
    }

    /**
     * 템플릿 생성
     */
    public function createTemplate(array $data): int
    {
        // 슬러그 자동 생성
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // 변수 목록 저장
        if (isset($data['variables']) && is_array($data['variables'])) {
            $data['variables'] = json_encode($data['variables']);
        }

        return DB::table('admin_email_templates')->insertGetId([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'subject' => $data['subject'],
            'body' => $data['body'],
            'variables' => $data['variables'] ?? null,
            'type' => $data['type'] ?? 'html',
            'is_active' => $data['is_active'] ?? true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * 템플릿 업데이트
     */
    public function updateTemplate(int $id, array $data): bool
    {
        // 슬러그 업데이트
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // 변수 목록 저장
        if (isset($data['variables']) && is_array($data['variables'])) {
            $data['variables'] = json_encode($data['variables']);
        }

        $data['updated_at'] = now();

        return DB::table('admin_email_templates')
            ->where('id', $id)
            ->update($data) > 0;
    }

    /**
     * 템플릿 삭제
     */
    public function deleteTemplate(int $id): bool
    {
        return DB::table('admin_email_templates')
            ->where('id', $id)
            ->delete() > 0;
    }
}