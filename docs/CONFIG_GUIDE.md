# @jiny/admin 설정 가이드

## 개요

@jiny/admin의 모든 설정은 `/jiny/admin/config/setting.php` 파일에서 관리됩니다.
`.env` 파일을 사용하지 않고 직접 설정 파일을 수정하여 사용합니다.

## 설정 파일 위치

```
/jiny/admin/config/setting.php
```

## 설정 읽기

모든 설정은 `config('admin.setting')` 헬퍼를 통해 접근합니다:

```php
// 전체 설정 가져오기
$settings = config('admin.setting');

// 특정 설정 가져오기
$captchaEnabled = config('admin.setting.captcha.enabled');
$maxAttempts = config('admin.setting.password.lockout.max_attempts');
```

## 주요 설정 항목

### 1. 기본 설정

```php
'name' => 'Jiny Admin',
'version' => '1.0.0',
'app_name' => 'Jiny Admin', // SMS/Email 템플릿에 사용
'app_url' => 'http://localhost:8000',
```

### 2. 비밀번호 정책

```php
'password' => [
    'min_length' => 8,
    'max_length' => 128,
    'require_uppercase' => true,
    'require_lowercase' => true,
    'require_numbers' => true,
    'require_special_chars' => true,
    'password_history' => 5, // 이전 비밀번호 재사용 방지
    'expiry_days' => 90, // 비밀번호 만료 기간
    
    'lockout' => [
        'max_attempts' => 5, // 최대 시도 횟수
        'lockout_duration' => 30, // 잠금 시간 (분)
        'warning_after_attempts' => 3,
    ],
],
```

### 3. 2단계 인증 (2FA)

```php
'password.two_factor' => [
    'enabled' => true,
    'required' => false, // 모든 사용자에게 강제할지 여부
    'methods' => ['totp', 'sms', 'email'],
    'default_method' => 'totp',
    'backup_codes' => 8,
    
    'totp' => [
        'issuer' => 'Jiny Admin',
        'digits' => 6,
        'period' => 30,
    ],
    
    'code' => [
        'length' => 6,
        'expiry_minutes' => 5,
        'resend_cooldown' => 60,
    ],
],
```

### 4. CAPTCHA 설정

```php
'captcha' => [
    'enabled' => false, // true로 변경하여 활성화
    'driver' => 'recaptcha', // recaptcha, hcaptcha, cloudflare
    'mode' => 'conditional', // always, conditional, disabled
    'show_after_attempts' => 3,
    
    'recaptcha' => [
        'site_key' => 'your-site-key',
        'secret_key' => 'your-secret-key',
        'version' => 'v2',
    ],
],
```

### 5. IP 제한 설정

```php
'ip_whitelist' => [
    'enabled' => false, // true로 변경하여 활성화
    'mode' => 'strict', // strict: 차단, log_only: 로그만
    
    'default_allowed' => [
        '127.0.0.1',
        '::1',
    ],
    
    'rate_limit' => [
        'max_attempts' => 5,
        'decay_minutes' => 15,
        'block_duration' => 60,
    ],
    
    'geoip' => [
        'enabled' => false,
        'allowed_countries' => ['KR', 'US', 'JP'],
    ],
],
```

### 6. SMS 설정

```php
'sms' => [
    'enabled' => false, // true로 변경하여 활성화
    'driver' => 'twilio', // twilio, vonage, aws_sns, aligo
    
    'twilio' => [
        'enabled' => false,
        'account_sid' => 'your-account-sid',
        'auth_token' => 'your-auth-token',
        'from' => '+1234567890',
    ],
    
    'templates' => [
        '2fa_code' => '[{app_name}] 인증 코드: {code}',
        'account_locked' => '[{app_name}] 계정이 잠금되었습니다. 잠금 해제: {unlock_url}',
    ],
],
```

### 7. 이메일 알림 설정

```php
'email' => [
    'notifications_enabled' => true,
    
    'events' => [
        'login_failed' => true,
        'account_locked' => true,
        'password_changed' => true,
        'two_fa_enabled' => true,
        'ip_blocked' => true,
    ],
    
    'admin_emails' => [
        'admin@example.com',
        'security@example.com',
    ],
],
```

### 8. 계정 잠금 해제 설정

```php
'unlock' => [
    'token_expiry' => 60, // 링크 유효 시간 (분)
    'max_attempts' => 5,
    'resend_cooldown' => 5, // 재발송 제한 (분)
    'use_security_question' => false,
],
```

## 설정 활성화 방법

### CAPTCHA 활성화

1. Google reCAPTCHA 사이트에서 키 발급
2. `setting.php` 수정:
```php
'captcha' => [
    'enabled' => true,
    'driver' => 'recaptcha',
    'recaptcha' => [
        'site_key' => 'your-actual-site-key',
        'secret_key' => 'your-actual-secret-key',
    ],
],
```

### SMS 2FA 활성화

1. Twilio 계정 생성 및 전화번호 구매
2. `setting.php` 수정:
```php
'sms' => [
    'enabled' => true,
    'driver' => 'twilio',
    'twilio' => [
        'enabled' => true,
        'account_sid' => 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        'auth_token' => 'your-auth-token',
        'from' => '+1234567890',
    ],
],
```

### IP 제한 활성화

```php
'ip_whitelist' => [
    'enabled' => true,
    'mode' => 'strict',
    'default_allowed' => [
        '127.0.0.1',
        '::1',
        '192.168.1.0/24', // 내부 네트워크
        '203.0.113.10', // 특정 IP
    ],
],
```

## 설정 캐시

설정을 변경한 후에는 캐시를 클리어해야 합니다:

```bash
php artisan config:clear
php artisan config:cache
```

## 보안 권장사항

1. **운영 환경에서는 반드시 설정하세요:**
   - CAPTCHA 활성화
   - IP 화이트리스트 활성화
   - 2FA 강제 적용
   - 강력한 비밀번호 정책

2. **민감한 정보 보호:**
   - API 키와 토큰은 안전하게 보관
   - 설정 파일에 대한 접근 권한 제한
   - 버전 관리 시스템에 실제 키 커밋 금지

3. **정기적인 검토:**
   - 비밀번호 만료 기간 검토
   - IP 화이트리스트 업데이트
   - 실패한 로그인 시도 모니터링

## 문제 해결

### CAPTCHA가 표시되지 않는 경우
- `captcha.enabled`가 `true`인지 확인
- 사이트 키가 올바른지 확인
- 브라우저 콘솔에서 JavaScript 오류 확인

### SMS가 발송되지 않는 경우
- `sms.enabled`가 `true`인지 확인
- Twilio 계정 잔액 확인
- 전화번호 형식이 올바른지 확인 (+국가코드 포함)

### IP가 차단된 경우
- 관리자 페이지에서 IP 차단 해제: `/admin/ipblacklist`
- CLI 명령: `php artisan admin:ip-unblock {ip}`

## 추가 문서

- [CAPTCHA 설정 가이드](captcha-setup-guide.md)
- [IP 화이트리스트 가이드](features/ip-whitelist.md)
- [2FA 설정 가이드](features/AdminUser2fa.md)