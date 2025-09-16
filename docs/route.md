# Admin Routes Documentation

## 개요

이 문서는 Jiny Admin 패키지의 라우트 구조와 구현 현황을 설명합니다.

## 라우트 파일 구조

```
jiny/admin/routes/
├── admin.php    # 메인 관리자 라우트
├── api.php      # API 라우트
└── web.php      # 웹 라우트
```

## 라우트 및 구현 현황

### ✅ 완료된 기능 (라우트 + 테이블)

| 기능 | 라우트 | 테이블 | 컨트롤러 |
|------|--------|--------|----------|
| **사용자 관리** | `/admin/users/*` | users (확장) | `AdminUsers\*` |
| **사용자 타입** | `/admin/user/type/*` | admin_user_types | `AdminUsertype\*` |
| **사용자 로그** | `/admin/user/logs/*` | admin_user_logs | `AdminUserLogs\*` |
| **사용자 세션** | `/admin/user/sessions/*` | admin_user_sessions | `AdminSessions\*` |
| **비밀번호 로그** | `/admin/user/password/logs/*` | admin_password_logs | `AdminPasswordLogs\*` |
| **비밀번호 관리** | `/admin/user/password/*` | admin_user_password_logs | `AdminUserPassword\*` |
| **2FA 관리** | `/admin/user/2fa/*` | admin_2fa_codes | `AdminUser2fa\*` |
| **계정 잠금 해제** | `/account/unlock/*` | admin_unlock_tokens | `UnlockAccount` |
| **IP 추적** | `/admin/iptracking/*` | admin_ip_attempts | `AdminIptracking\*` |
| **IP 블랙리스트** | `/admin/ipblacklist/*` | admin_ip_blacklist | `AdminIpblacklist\*` |
| **IP 화이트리스트** | `/admin/ipwhitelist/*`, `/admin/security/ip-whitelist/*` | admin_ip_whitelist | `AdminIpwhitelist\*` |
| **CAPTCHA 로그** | `/admin/user/captcha/logs/*` | admin_captcha_logs | `AdminCaptchaLogs\*` |
| **이메일 템플릿** | `/admin/mail/templates/*` | admin_email_templates | `AdminEmailTemplates\*` |
| **이메일 로그** | `/admin/mail/logs/*` | admin_email_logs | `AdminEmailLogs\*` |
| **SMS 제공자** | `/admin/sms/provider/*` | admin_sms_providers | `AdminSmsProvider\*` |
| **SMS 발송** | `/admin/sms/send/*` | admin_sms_sends | `AdminSmsSend\*` |
| **아바타 관리** | `/admin/user/avatar/*` | users.avatar | `AdminAvatar\*` |
| **메일 설정** | `/admin/mail/setting/*` | .env 또는 config | `AdminMailSetting` |

### ⚠️ 부분 완료 (라우트는 있지만 테이블 미구현)

| 기능 | 라우트 | 필요 테이블 | 상태 |
|------|--------|------------|------|
| **알림 설정** | `/admin/settings/notifications/*` | admin_webhook_configs, admin_webhook_logs | ⚠️ 테이블 있음, 추가 기능 필요 |
| **푸시 알림** | `/admin/settings/notifications/push/*` | admin_push_providers, admin_push_devices, admin_push_logs | ⚠️ 테이블 있음, 컨트롤러 구현 필요 |
| **이메일 추적** | `/admin/mail/tracking/*` | admin_email_tracking | ❌ 테이블 없음 |
| **메일 대시보드** | `/admin/mail/` | - | ⚠️ 대시보드용 |
| **사용자 통계** | `/admin/user/stats` | - | ⚠️ 집계 쿼리 사용 |

### ❌ 미구현 기능 (테이블은 있지만 라우트 없음)

| 기능 | 예상 라우트 | 테이블 | 우선순위 |
|------|------------|--------|----------|
| **IP 로그** | `/admin/security/ip-logs/*` | admin_ip_logs | 중 |
| **IP 접근 로그** | `/admin/security/access-logs/*` | admin_ip_access_logs | 중 |
| **CAPTCHA 화이트/블랙리스트** | `/admin/captcha/whitelist-blacklist/*` | admin_captcha_whitelist_blacklist | 낮음 |
| **이메일 알림 규칙** | `/admin/mail/notification-rules/*` | admin_email_notification_rules | 높음 |
| **이메일 템플릿 버전** | `/admin/mail/template-versions/*` | admin_email_template_versions | 낮음 |
| **이메일 A/B 테스트** | `/admin/mail/ab-tests/*` | admin_email_ab_tests | 낮음 |
| **이메일 구독자** | `/admin/mail/subscribers/*` | admin_email_subscribers | 중 |
| **SMS 큐** | `/admin/sms/queue/*` | admin_sms_queues | 높음 |
| **SMS 웹훅** | `/admin/sms/webhooks/*` | admin_sms_webhooks | 중 |

## 라우트 상세 정보

### 사용자 관리 라우트

#### 기본 사용자 CRUD
```php
Route::prefix('admin/users')->group(function () {
    Route::get('/', AdminUsers::class);                    // 목록
    Route::get('/create', AdminUsersCreate::class);        // 생성 폼
    Route::get('/{id}/edit', AdminUsersEdit::class);       // 수정 폼
    Route::get('/{id}', AdminUsersShow::class);            // 상세
    Route::delete('/{id}', AdminUsersDelete::class);       // 삭제
});
```

#### 2FA 관리
```php
Route::prefix('admin/user/2fa')->group(function () {
    Route::post('/{id}/generate', 'generate');             // QR 생성
    Route::post('/{id}/store', 'store');                   // 저장
    Route::post('/{id}/regenerate-backup', 'regenerateBackup'); // 백업코드 재생성
    Route::post('/send-sms', 'sendSmsCode');               // SMS 발송
    Route::post('/verify-sms', 'verifySmsCode');           // SMS 검증
    Route::post('/send-email', 'sendEmailCode');           // 이메일 발송
    Route::post('/verify-email', 'verifyEmailCode');       // 이메일 검증
});
```

### 보안 관리 라우트

#### IP 관리
```php
// IP 화이트리스트
Route::prefix('admin/security/ip-whitelist')->group(function () {
    Route::get('/', AdminIpWhitelist::class);
    Route::get('/create', AdminIpWhitelistCreate::class);
    Route::get('/{id}/edit', AdminIpWhitelistEdit::class);
    Route::delete('/{id}', AdminIpWhitelistDelete::class);
});

// IP 블랙리스트
Route::prefix('admin/ipblacklist')->group(function () {
    Route::get('/', AdminIpblacklist::class);
    Route::get('/create', AdminIpblacklistCreate::class);
    Route::get('/{id}/edit', AdminIpblacklistEdit::class);
    Route::delete('/{id}', AdminIpblacklistDelete::class);
});

// IP 추적
Route::prefix('admin/iptracking')->group(function () {
    Route::get('/', AdminIptracking::class);
    Route::get('/{id}', AdminIptrackingShow::class);
});
```

#### CAPTCHA 관리
```php
Route::prefix('admin/user/captcha/logs')->group(function () {
    Route::get('/', 'index');                              // 목록
    Route::get('/list', 'list');                           // JSON 목록
    Route::get('/export', 'export');                       // 내보내기
    Route::post('/block-ip', 'blockIp');                   // IP 차단
    Route::post('/cleanup', 'cleanupLogs');                // 로그 정리
});
```

### 이메일 관리 라우트

#### 이메일 템플릿
```php
Route::prefix('admin/mail/templates')->group(function () {
    Route::get('/', AdminEmailTemplates::class);
    Route::get('/create', AdminEmailTemplatesCreate::class);
    Route::get('/{id}/edit', AdminEmailTemplatesEdit::class);
    Route::get('/{id}', AdminEmailTemplatesShow::class);
    Route::delete('/{id}', AdminEmailTemplatesDelete::class);
});
```

#### 이메일 로그
```php
Route::prefix('admin/mail/logs')->group(function () {
    Route::get('/', AdminEmailLogs::class);
    Route::post('/{id}/send', AdminEmailLogsSend::class);      // 발송
    Route::post('/{id}/resend', AdminEmailLogsResend::class);  // 재발송
});
```

#### 이메일 추적 (공개)
```php
Route::prefix('admin/mail/tracking')->withoutMiddleware(['auth', 'admin'])->group(function () {
    Route::get('/pixel/{token}', 'pixel');                 // 픽셀 추적
    Route::get('/link/{token}/{linkId}', 'link');          // 링크 추적
    Route::get('/stats/{emailId}', 'stats');               // 통계
});
```

### SMS 관리 라우트

#### SMS 제공자
```php
Route::prefix('admin/sms/provider')->group(function () {
    Route::get('/', AdminSmsProvider::class);
    Route::get('/create', AdminSmsProviderCreate::class);
    Route::get('/{id}/edit', AdminSmsProviderEdit::class);
    Route::delete('/{id}', AdminSmsProviderDelete::class);
});
```

#### SMS 발송
```php
Route::prefix('admin/sms/send')->group(function () {
    Route::get('/', AdminSmsSend::class);
    Route::get('/create', AdminSmsSendCreate::class);
    Route::post('/{id}/send', 'send');                     // 발송
    Route::post('/bulk-send', 'sendBulk');                 // 대량 발송
    Route::post('/{id}/resend', 'resend');                 // 재발송
});
```

### 알림 설정 라우트

#### 웹훅 관리
```php
Route::prefix('admin/settings/notifications')->group(function () {
    Route::get('/webhooks', 'webhooks');                   // 목록
    Route::get('/webhooks/create', 'createWebhook');       // 생성
    Route::post('/webhooks', 'storeWebhook');              // 저장
    Route::get('/webhooks/{id}/edit', 'editWebhook');      // 수정
    Route::put('/webhooks/{id}', 'updateWebhook');         // 업데이트
    Route::delete('/webhooks/{id}', 'deleteWebhook');      // 삭제
    Route::post('/webhooks/{id}/test', 'testWebhook');     // 테스트
});
```

#### 푸시 알림
```php
Route::prefix('admin/settings/notifications')->group(function () {
    Route::get('/push', 'pushSettings');                   // 설정
    Route::post('/push/vapid', 'generateVapidKeys');       // VAPID 키 생성
});
```

## 미들웨어 사용

### 기본 미들웨어
- `web`: 웹 세션, CSRF 보호
- `auth`: 인증 필요
- `admin`: 관리자 권한 필요

### 미들웨어 조합 예시
```php
// 관리자 전용
Route::middleware(['web', 'auth', 'admin'])->group(...)

// 인증만 필요
Route::middleware(['web', 'auth'])->group(...)

// 공개 접근 (추적 픽셀 등)
Route::withoutMiddleware(['auth', 'admin'])->group(...)
```

## 구현 우선순위

### Phase 1 - 즉시 구현 필요
2. **SMS 큐 라우트** (`/admin/sms/queue/*`)

### Phase 2 - 보안 강화
1. **IP 로그 라우트** (`/admin/security/ip-logs/*`)
2. **IP 접근 로그 라우트** (`/admin/security/access-logs/*`)
3. **CAPTCHA 화이트/블랙리스트 라우트**

### Phase 3 - 고급 기능
1. **이메일 고급 기능 라우트**
   - 알림 규칙
   - 템플릿 버전 관리
   - A/B 테스트
   - 구독자 관리

2. **SMS 고급 기능 라우트**
   - SMS 큐 관리
   - 웹훅 처리

## 네이밍 컨벤션

### 라우트 URL
- 소문자 사용: `/admin/users`
- 하이픈 구분: `/admin/ip-whitelist`
- RESTful 패턴: `GET /admin/users/{id}/edit`

### 컨트롤러 클래스
- PascalCase: `AdminUsers`
- 액션별 분리: `AdminUsersCreate`, `AdminUsersEdit`
- 네임스페이스: `Jiny\Admin\App\Http\Controllers\Admin\*`

### 라우트 이름
- 점 표기법: `admin.users.create`
- 계층 구조: `admin.mail.templates.edit`
- 동사 사용: `admin.sms.send.action`
