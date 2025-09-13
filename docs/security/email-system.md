# 메일 시스템과 알림

## 개요

Jiny Admin의 메일 시스템은 템플릿 기반의 동적 이메일 발송, 이벤트 기반 자동 알림, 다국어 지원, 발송 추적 및 재발송 기능을 제공합니다.

## 아키텍처

```
┌─────────────────────────────────────────────────────────┐
│                    NotificationService                    │
│                   (통합 알림 컨트롤러)                      │
└────────────┬────────────────────────────────────────────┘
             │
    ┌────────┴────────┬────────────┬──────────────┐
    ▼                 ▼            ▼              ▼
EmailTemplateService  SmsService  WebhookService  PushService
    │                 │            │              │
    ▼                 ▼            ▼              ▼
EmailLogService    Twilio/Vonage  Slack/Discord  FCM/WebPush
```

## 핵심 서비스

### 1. EmailTemplateService

**위치**: `App\Services\EmailTemplateService.php`

**주요 기능**:
- 템플릿 관리 (CRUD)
- 변수 치환 및 렌더링
- 레이아웃 시스템
- 캐싱

**사용 예시**:

```php
// 템플릿 가져오기
$template = $emailTemplateService->getTemplate('welcome_email');

// 템플릿 렌더링
$rendered = $emailTemplateService->render($template, [
    'user_name' => '홍길동',
    'activation_link' => 'https://example.com/activate/123'
]);

// 결과
// $rendered = [
//     'subject' => '환영합니다, 홍길동님!',
//     'body' => '<html>...</html>',
//     'type' => 'html'
// ]
```

### 2. NotificationService

**위치**: `App\Services\NotificationService.php`

**주요 메서드**:

```php
/**
 * 호출 관계 트리:
 * 
 * notify()
 * ├── getActiveRules()          // 활성 규칙 조회
 * ├── checkConditions()          // 조건 확인
 * ├── checkThrottle()            // 스로틀링 체크
 * ├── determineRecipients()      // 수신자 결정
 * ├── executeHooks('before_send') // 발송 전 Hook
 * ├── sendNotification()         // 알림 발송
 * │   ├── templateService->render()
 * │   ├── logService->createLog()
 * │   └── Mail::send()
 * ├── executeHooks('after_send')  // 발송 후 Hook
 * └── updateRuleStatistics()      // 통계 업데이트
 */
public function notify(string $eventType, array $data = []): bool
```

**멀티채널 발송**:

```php
/**
 * 호출 관계 트리:
 * 
 * notifyMultiChannel()
 * ├── getEventChannels()         // 이벤트 채널 조회
 * ├── notify()                   // 이메일 발송
 * ├── smsService->send()         // SMS 발송
 * ├── webhookService->sendByEvent() // 웹훅 발송
 * ├── pushService->send()        // 푸시 발송
 * └── logMultiChannelNotification() // 로그 기록
 */
public function notifyMultiChannel(
    string $eventType, 
    array $data, 
    array $channels = []
): array
```

## 이벤트 타입

### 시스템 이벤트

| 이벤트 | 설명 | 트리거 조건 | 기본 채널 |
|--------|------|------------|----------|
| `login_failed` | 로그인 실패 | 실패 횟수 > 3 | 이메일 |
| `account_locked` | 계정 잠금 | 최대 시도 초과 | 이메일, SMS |
| `password_changed` | 비밀번호 변경 | 변경 완료 시 | 이메일 |
| `two_fa_enabled` | 2FA 활성화 | 설정 변경 시 | 이메일 |
| `two_fa_disabled` | 2FA 비활성화 | 설정 변경 시 | 이메일 |
| `ip_blocked` | IP 차단 | 의심 활동 감지 | 이메일, 웹훅 |

### 커스텀 이벤트 등록

```php
// 새 이벤트 규칙 생성
$notificationService->createRule([
    'name' => '대량 데이터 삭제 알림',
    'event_type' => 'bulk_delete',
    'conditions' => [
        'count' => ['operator' => '>', 'value' => 100]
    ],
    'recipient_type' => 'admin',
    'template_slug' => 'bulk_delete_alert',
    'priority' => 'high'
]);
```

## 템플릿 변수 시스템

### 기본 제공 변수

```php
// 모든 템플릿에서 사용 가능
{{app_name}}        // 애플리케이션 이름
{{app_url}}         // 애플리케이션 URL
{{current_year}}    // 현재 연도
{{current_date}}    // 현재 날짜
{{current_time}}    // 현재 시간
```

### 조건문 사용

```html
{{#if premium_user}}
    <p>프리미엄 회원님을 위한 특별 혜택!</p>
{{/if}}
```

### 반복문 사용

```html
{{#each items}}
    <li>{{name}} - {{price}}원</li>
{{/each}}
```

## Hook 시스템

### Hook 등록

```php
// 발송 전 Hook
$notificationService->registerHook('before_send_login_failed', 
    function($data, $recipient) {
        // IP 지역 확인
        if ($data['country'] === 'suspicious') {
            // 추가 보안 조치
            return false; // 발송 취소
        }
        return true;
    }
);

// 발송 후 Hook
$notificationService->registerHook('after_send_account_locked',
    function($data, $recipient, $result) {
        if ($result) {
            // Slack에 추가 알림
            SlackNotifier::alert('계정 잠금: ' . $recipient['email']);
        }
    }
);
```

## 발송 로그 및 추적

### 로그 구조

```sql
admin_email_logs
├── id
├── template_id       // 사용된 템플릿
├── to_email         // 수신자
├── subject          // 제목
├── body            // 본문
├── status          // pending|sent|failed|bounced
├── sent_at         // 발송 시각
├── opened_at       // 열람 시각
├── clicked_at      // 클릭 시각
└── error_message   // 오류 메시지
```

### 재발송

```php
// 실패한 이메일 재발송
$failedEmails = EmailLog::where('status', 'failed')
    ->where('created_at', '>', now()->subHours(24))
    ->get();

foreach ($failedEmails as $log) {
    $emailLogService->resend($log->id);
}
```

## 성능 최적화

### 캐싱 전략

```php
// 템플릿 캐싱 (1시간)
Cache::remember('email_template:' . $slug, 3600, function() {
    return DB::table('admin_email_templates')
        ->where('slug', $slug)
        ->first();
});
```

### 큐 사용

```php
// 대량 발송 시 큐 사용
NotificationJob::dispatch($eventType, $data)
    ->onQueue('notifications')
    ->delay(now()->addSeconds(10));
```

## 테스트 가이드

### 1. 템플릿 테스트

```php
// 테스트 발송
$emailTemplateService->testTemplate(
    $templateId,
    'test@example.com',
    ['test_variable' => '테스트 값']
);
```

### 2. 이벤트 시뮬레이션

```php
// 로그인 실패 이벤트 테스트
$notificationService->notifyLoginFailed(
    'test@example.com',
    5,  // 실패 횟수
    '192.168.1.1'
);
```

### 3. 멀티채널 테스트

```php
// 모든 채널로 테스트
$result = $notificationService->notifyMultiChannel(
    'test_event',
    ['message' => '테스트 메시지'],
    ['email', 'sms', 'webhook', 'push']
);

// 결과 확인
var_dump($result);
// ['email' => true, 'sms' => true, 'webhook' => [...], 'push' => true]
```

## 문제 해결

### 이메일이 발송되지 않음

1. 메일 설정 확인 (`config/mail.php`)
2. 템플릿 활성화 상태 확인
3. 수신자 이메일 유효성 확인
4. 로그 확인 (`admin_email_logs`)

### 템플릿 변수가 치환되지 않음

1. 변수명 정확성 확인
2. 변수 값이 scalar 타입인지 확인
3. 템플릿 문법 확인 (`{{variable}}`)

### 알림 규칙이 작동하지 않음

1. 규칙 활성화 상태 확인
2. 조건 설정 확인
3. 이벤트 타입 매칭 확인
4. 스로틀링 설정 확인

## 보안 고려사항

1. **템플릿 인젝션 방지**: 사용자 입력은 항상 이스케이프
2. **발송 제한**: Rate limiting 적용
3. **수신자 검증**: 이메일 주소 유효성 검사
4. **로그 암호화**: 민감한 정보는 암호화하여 저장
5. **DKIM/SPF 설정**: 이메일 신뢰도 향상

## 관련 파일

- `App\Services\EmailTemplateService.php`
- `App\Services\NotificationService.php`
- `App\Services\EmailLogService.php`
- `App\Models\AdminEmailTemplate.php`
- `App\Models\AdminEmailLog.php`
- `database\migrations\*_create_admin_email_*.php`