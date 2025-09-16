
# Webhook 시스템 문서

## 개요

Jiny Admin의 Webhook 시스템은 다양한 외부 서비스(Slack, Discord, Teams 등)로 알림을 발송할 수 있는 통합 알림 시스템입니다. 이벤트 기반 자동 발송, 채널 관리, 로그 추적 등의 기능을 제공합니다.

## 주요 기능

### 1. 웹훅 대시보드 (`/admin/webhook`)
- **통계 모니터링**: 실시간 발송 통계, 성공률, 채널 상태 확인
- **일별 발송 추이**: 최근 7일간 발송 현황 차트
- **채널 성능 분석**: 채널별 성공률 및 사용 빈도 분석
- **최근 활동 로그**: 최근 발송된 웹훅 메시지 확인

### 2. 채널 관리 (`/admin/webhook/channels`)
- **다양한 플랫폼 지원**: Slack, Discord, Microsoft Teams, Custom Webhook
- **채널 설정**: 웹훅 URL, 커스텀 헤더, 우선순위 설정
- **활성화/비활성화**: 채널별 활성화 상태 관리
- **테스트 발송**: 채널 연결 상태 테스트

### 3. 로그 관리 (`/admin/webhook/logs`)
- **발송 이력 추적**: 모든 웹훅 발송 내역 기록
- **상태 모니터링**: 성공/실패 상태 및 에러 메시지 확인
- **필터링 및 검색**: 날짜, 채널, 상태별 로그 필터링

## 아키텍처

### 핵심 컴포넌트

#### 1. WebhookService
- **위치**: `jiny/admin/App/Services/Notifications/WebhookService.php`
- **기능**: 
  - 웹훅 발송 핵심 로직
  - 플랫폼별 메시지 포맷팅
  - 채널 관리 및 캐싱
  - 이벤트 기반 자동 발송

#### 2. 데이터베이스 테이블

##### admin_webhook_channels
```sql
- id: 채널 ID
- name: 채널 이름 (unique)
- type: 웹훅 타입 (slack/discord/teams/custom)
- webhook_url: 웹훅 URL
- headers: 커스텀 헤더 (JSON)
- config: 추가 설정 (JSON)
- description: 채널 설명
- is_active: 활성화 여부
- priority: 우선순위
- timestamps
```

##### admin_webhook_logs
```sql
- id: 로그 ID
- channel_name: 채널 이름
- message: 발송 메시지
- status: 발송 상태 (sent/failed)
- error_message: 에러 메시지
- sent_at: 발송 시간
- created_at: 생성 시간
```

##### admin_webhook_subscriptions
```sql
- channel_name: 채널 이름
- event_type: 이벤트 타입
- is_active: 활성화 여부
- created_at: 생성 시간
```

### 라우트 구조
```php
/admin/webhook                    # 대시보드
/admin/webhook/channels           # 채널 목록
/admin/webhook/channels/create    # 채널 생성
/admin/webhook/channels/{id}/edit # 채널 수정
/admin/webhook/channels/{id}      # 채널 상세
/admin/webhook/channels/{id}/test # 채널 테스트
/admin/webhook/logs               # 로그 목록
/admin/webhook/logs/{id}          # 로그 상세
```

## 사용 방법

### 1. 웹훅 채널 설정

#### Slack 채널 추가
```php
use Jiny\Admin\App\Services\Notifications\WebhookService;

$webhookService = new WebhookService();
$channelData = [
    'name' => 'slack_alerts',
    'type' => 'slack',
    'webhook_url' => 'https://hooks.slack.com/services/YOUR/WEBHOOK/URL',
    'description' => 'Slack 알림 채널',
    'custom_headers' => ['Content-Type' => 'application/json'],
    'is_active' => true
];
$channelId = $webhookService->createChannel($channelData);
```

#### Discord 채널 추가
```php
$channelData = [
    'name' => 'discord_notifications',
    'type' => 'discord',
    'webhook_url' => 'https://discord.com/api/webhooks/YOUR/WEBHOOK/URL',
    'description' => 'Discord 알림 채널',
    'is_active' => true
];
$channelId = $webhookService->createChannel($channelData);
```

### 2. 웹훅 발송

#### 단일 채널 발송
```php
$webhookService = new WebhookService();

// 간단한 메시지 발송
$result = $webhookService->send('slack_alerts', '새로운 사용자가 가입했습니다.');

// 추가 데이터와 함께 발송
$data = [
    'color' => 'success',
    'title' => '사용자 가입',
    'user_name' => 'John Doe',
    'user_email' => 'john@example.com',
    'registered_at' => now()->toDateTimeString()
];
$result = $webhookService->send('slack_alerts', '새로운 사용자 가입', $data);
```

#### 다중 채널 발송
```php
$channels = ['slack_alerts', 'discord_notifications'];
$message = '중요 시스템 알림';
$results = $webhookService->sendToMultiple($channels, $message, $data);
```

#### 이벤트 기반 자동 발송
```php
// 이벤트 구독 설정
$webhookService->setEventSubscription('slack_alerts', 'user.registered', true);

// 이벤트 발생 시 자동 발송
$webhookService->sendByEvent('user.registered', '새 사용자 가입', [
    'user_id' => $user->id,
    'user_name' => $user->name
]);
```

### 3. 메시지 포맷팅

#### Slack 메시지 포맷
```php
$data = [
    'color' => 'danger',  // good, warning, danger, info
    'title' => '서버 오류',
    'error_code' => '500',
    'error_message' => 'Internal Server Error',
    'mention' => '@channel',  // 멘션 추가
    'action_url' => 'https://example.com/logs'
];
```

#### Discord 메시지 포맷
```php
$data = [
    'title' => 'Discord 알림',
    'description' => '상세 설명',
    'color' => 'info',  // danger, warning, info, success, primary, secondary
    'field1' => 'value1',
    'field2' => 'value2',
    'mention' => '@everyone'
];
```

#### Teams 메시지 포맷
```php
$data = [
    'title' => 'Teams 알림',
    'color' => 'success',
    'fact1' => 'value1',
    'fact2' => 'value2',
    'action_url' => 'https://example.com',
    'action_text' => '자세히 보기'
];
```

## 고급 기능

### 1. 웹훅 테스트
```php
// 채널 연결 테스트
$result = $webhookService->testChannel('slack_alerts');
if ($result) {
    echo "채널 연결 성공";
} else {
    echo "채널 연결 실패";
}
```

### 2. 캐시 관리
```php
// 채널 설정 캐시 클리어
$webhookService->clearCache();
```

### 3. 대량 발송 (Bulk Send)
```php
// 여러 수신자에게 개별 메시지 발송
$recipients = [
    ['channel' => 'slack_alerts', 'message' => '메시지1'],
    ['channel' => 'discord_notifications', 'message' => '메시지2'],
];

foreach ($recipients as $recipient) {
    $webhookService->send($recipient['channel'], $recipient['message']);
}
```

## 보안 고려사항

1. **웹훅 URL 보호**: 웹훅 URL은 데이터베이스에 암호화하여 저장
2. **Rate Limiting**: 채널별 발송 속도 제한 적용
3. **인증 헤더**: 필요시 Authorization 헤더 추가
4. **HTTPS 사용**: 모든 웹훅 URL은 HTTPS 프로토콜 사용 권장
5. **로그 정리**: 오래된 로그는 주기적으로 정리

## 문제 해결

### 웹훅 발송 실패
1. 웹훅 URL이 올바른지 확인
2. 네트워크 연결 상태 확인
3. 타임아웃 설정 확인 (기본 10초)
4. 로그에서 상세 에러 메시지 확인

### 메시지 포맷 오류
1. 각 플랫폼의 메시지 포맷 규격 확인
2. JSON 인코딩 오류 확인
3. 특수문자 이스케이프 처리

### 성능 최적화
1. 캐시 활용 (1시간 TTL)
2. 비동기 발송 고려 (Queue 사용)
3. 배치 발송 활용

## 예제 시나리오

### 시나리오 1: 사용자 가입 알림
```php
// 사용자 가입 이벤트 리스너에서
public function handle(UserRegistered $event)
{
    $webhookService = new WebhookService();
    
    $message = sprintf(
        "🎉 새로운 사용자가 가입했습니다!\n이름: %s\n이메일: %s",
        $event->user->name,
        $event->user->email
    );
    
    $data = [
        'color' => 'success',
        'title' => '신규 회원 가입',
        'user_id' => $event->user->id,
        'registered_at' => $event->user->created_at
    ];
    
    $webhookService->sendByEvent('user.registered', $message, $data);
}
```

### 시나리오 2: 시스템 오류 알림
```php
// 예외 핸들러에서
public function report(Exception $exception)
{
    if ($this->shouldReport($exception)) {
        $webhookService = new WebhookService();
        
        $message = sprintf(
            "⚠️ 시스템 오류 발생\n%s\n위치: %s:%d",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
        
        $data = [
            'color' => 'danger',
            'title' => 'System Error',
            'error_class' => get_class($exception),
            'trace' => substr($exception->getTraceAsString(), 0, 500),
            'mention' => '@channel'
        ];
        
        $webhookService->send('slack_critical', $message, $data);
    }
}
```

### 시나리오 3: 일일 리포트
```php
// 스케줄러에서
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $webhookService = new WebhookService();
        $stats = $this->getDailyStats();
        
        $message = sprintf(
            "📊 일일 리포트\n신규 가입: %d명\n총 주문: %d건\n매출: %s원",
            $stats['new_users'],
            $stats['orders'],
            number_format($stats['revenue'])
        );
        
        $webhookService->sendToMultiple(
            ['slack_reports', 'teams_management'],
            $message,
            ['color' => 'info']
        );
    })->dailyAt('09:00');
}
```

## 마이그레이션 가이드

### 기존 시스템에서 마이그레이션
```php
// 1. 기존 웹훅 URL 수집
$oldWebhooks = [
    'slack' => env('SLACK_WEBHOOK_URL'),
    'discord' => env('DISCORD_WEBHOOK_URL'),
];

// 2. 새 시스템에 채널 등록
$webhookService = new WebhookService();
foreach ($oldWebhooks as $type => $url) {
    if ($url) {
        $webhookService->createChannel([
            'name' => $type . '_legacy',
            'type' => $type,
            'webhook_url' => $url,
            'is_active' => true
        ]);
    }
}

// 3. 코드 업데이트
// 기존: Http::post($webhookUrl, $payload)
// 신규: $webhookService->send('channel_name', $message, $data)
```

## 관련 링크

- [Slack Incoming Webhooks](https://api.slack.com/messaging/webhooks)
- [Discord Webhooks](https://discord.com/developers/docs/resources/webhook)
- [Microsoft Teams Webhooks](https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook)

## 업데이트 내역

### v1.0.0 (2025-01-01)
- 초기 버전 릴리스
- Slack, Discord, Teams, Custom 웹훅 지원
- 이벤트 기반 자동 발송 기능
- 웹훅 대시보드 및 관리 인터페이스
- 발송 로그 및 통계 기능
