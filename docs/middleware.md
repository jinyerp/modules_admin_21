# Jiny Admin 미들웨어 가이드

## 개요

Jiny Admin 패키지는 관리자 시스템의 보안과 접근 제어를 위한 4개의 핵심 미들웨어를 제공합니다. 각 미들웨어는 설정 파일을 통해 동작을 제어할 수 있으며, 필요에 따라 선택적으로 적용할 수 있습니다.

## 미들웨어 목록

### 1. AdminMiddleware (`admin`)

**목적:** 관리자 페이지 접근 권한 제어

**주요 기능:**
- 인증 상태 확인
- 관리자 권한 검증 (isAdmin, utype)
- 사용자 타입 유효성 검증 (DB 조회)
- 특정 권한 요구사항 처리
- 계정 상태 확인 (활성화/차단)
- 활동 시간 추적

**사용 예시:**
```php
// 라우트에서 사용
Route::middleware(['web', 'admin'])->group(function () {
    // 관리자만 접근 가능한 라우트
});

// 특정 권한 타입 요구
Route::middleware(['web', 'admin:super'])->group(function () {
    // super 관리자만 접근 가능
});
```

### 2. IpWhitelistMiddleware (`ip.whitelist`)

**목적:** IP 기반 접근 제한

**주요 기능:**
- IP 화이트리스트 검증
- 단일 IP, IP 범위, CIDR 표기법 지원
- 접근 로그 기록
- 캐시를 통한 성능 최적화

**설정:**
```php
// config/admin/setting.php
'ip_whitelist' => [
    'enabled' => true,  // 기능 활성화
    'cache' => [
        'key' => 'admin_ip_whitelist',
        'ttl' => 300,  // 5분
    ],
    'default_allowed' => [
        '127.0.0.1',
        '::1',  // IPv6 localhost
    ],
    'trusted_proxies' => [],  // 프록시 서버 IP
],
```

**사용 예시:**
```php
// strict 모드 (차단)
Route::middleware(['web', 'ip.whitelist'])->group(function () {
    // IP 화이트리스트에 있는 IP만 접근 가능
});

// log_only 모드 (로그만 기록)
Route::middleware(['web', 'ip.whitelist:log_only'])->group(function () {
    // 모든 IP 접근 허용, 로그만 기록
});
```

### 3. CaptchaMiddleware (`captcha`)

**목적:** 자동화된 봇 공격 방지

**주요 기능:**
- CAPTCHA 필요 여부 판단
- 다양한 CAPTCHA 서비스 지원 (reCAPTCHA, hCaptcha, Cloudflare Turnstile)
- 실패 시도 추적
- 로그 기록

**설정:**
```php
// config/admin/setting.php
'captcha' => [
    'enabled' => true,  // CAPTCHA 활성화
    'mode' => 'smart',  // 'always' 또는 'smart'
    'log' => [
        'enabled' => true,
        'failed_only' => false,  // 실패만 로그
    ],
    'messages' => [
        'required' => 'CAPTCHA 인증이 필요합니다.',
        'failed' => 'CAPTCHA 인증에 실패했습니다.',
        'not_configured' => 'CAPTCHA 설정 오류가 발생했습니다.',
    ],
],
```

**사용 예시:**
```php
Route::middleware(['web', 'captcha'])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
});
```

### 4. CheckPasswordChange (`check.password.change`)

**목적:** 비밀번호 변경 필요 여부 확인

**주요 기능:**
- 비밀번호 만료 체크
- 강제 변경 플래그 확인
- 비밀번호 변경 페이지로 자동 리다이렉트

**사용 예시:**
```php
Route::middleware(['web', 'auth', 'check.password.change'])->group(function () {
    // 비밀번호 변경이 필요한 경우 자동으로 변경 페이지로 이동
});
```

## 미들웨어 적용 전략

### 현재 상황

현재 대부분의 라우트는 `admin` 미들웨어만 사용하고 있습니다. 다른 미들웨어들은 설정에 따라 동적으로 적용되어야 합니다.

### 권장 적용 방법

#### 1. 컨트롤러 Constructor 방식 (권장)

설정에 따라 동적으로 미들웨어를 적용할 때 유용합니다.

```php
<?php

namespace Jiny\Admin\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class AdminBaseController extends Controller
{
    public function __construct()
    {
        // 기본 admin 미들웨어
        $this->middleware('admin');
        
        // IP 화이트리스트 (설정에 따라)
        if (config('admin.setting.ip_whitelist.enabled')) {
            $this->middleware('ip.whitelist');
        }
        
        // 비밀번호 변경 체크 (설정에 따라)
        if (config('admin.setting.password.check_expiry')) {
            $this->middleware('check.password.change');
        }
        
        // CAPTCHA (특정 액션에만)
        if (config('admin.setting.captcha.enabled')) {
            $this->middleware('captcha')->only(['store', 'update', 'destroy']);
        }
    }
}
```

**장점:**
- 설정 기반 동적 제어 가능
- 중앙 집중식 관리
- 메서드별 세밀한 제어
- 코드 재사용성 높음

**단점:**
- 라우트 정의에서 미들웨어가 보이지 않음
- 디버깅 시 추적이 어려울 수 있음

#### 2. 라우트 그룹 방식

정적이고 명확한 미들웨어 적용이 필요할 때 사용합니다.

```php
// routes/admin.php
Route::middleware(['web'])->group(function () {
    
    // 로그인 라우트 (CAPTCHA 적용)
    Route::middleware(['captcha'])->group(function () {
        Route::post('/login', [AdminLogin::class, 'login']);
    });
    
    // 일반 관리자 라우트
    Route::middleware(['admin'])->group(function () {
        // 기본 관리자 페이지
        Route::get('/dashboard', [AdminDashboard::class, 'index']);
    });
    
    // 보안 강화 라우트 (IP 화이트리스트 + 비밀번호 체크)
    Route::middleware(['admin', 'ip.whitelist', 'check.password.change'])->group(function () {
        // 민감한 관리 기능
        Route::resource('/users', AdminUsersController::class);
        Route::resource('/settings', AdminSettingsController::class);
    });
});
```

**장점:**
- 라우트 정의에서 명확히 보임
- 디버깅 용이
- 그룹별 일괄 적용

**단점:**
- 설정 기반 동적 제어 어려움
- 코드 중복 가능성

#### 3. 하이브리드 방식 (최적 권장)

기본 미들웨어는 라우트에서, 선택적 미들웨어는 컨트롤러에서 처리합니다.

```php
// routes/admin.php
Route::middleware(['web', 'admin'])->group(function () {
    // 모든 관리자 라우트는 기본적으로 admin 미들웨어 적용
    Route::resource('/users', AdminUsersController::class);
});

// AdminUsersController.php
class AdminUsersController extends Controller
{
    public function __construct()
    {
        // 설정 기반 추가 미들웨어
        $this->applyConfigBasedMiddleware();
    }
    
    protected function applyConfigBasedMiddleware()
    {
        // IP 화이트리스트
        if (config('admin.setting.security.ip_whitelist')) {
            $this->middleware('ip.whitelist');
        }
        
        // 비밀번호 만료 체크
        if (config('admin.setting.password.check_expiry')) {
            $this->middleware('check.password.change')
                 ->except(['passwordChangeForm', 'passwordChange']);
        }
        
        // CAPTCHA (민감한 작업에만)
        if (config('admin.setting.security.captcha_for_admin')) {
            $this->middleware('captcha')
                 ->only(['destroy', 'massDestroy']);
        }
    }
}
```

## 미들웨어 우선순위

미들웨어는 다음 순서로 적용하는 것이 권장됩니다:

1. `web` - 세션, CSRF 등 기본 웹 기능
2. `ip.whitelist` - IP 차단 (가장 먼저 체크)
3. `admin` - 인증 및 권한 확인
4. `check.password.change` - 비밀번호 변경 필요 체크
5. `captcha` - CAPTCHA 검증 (POST 요청에만)

## 설정 파일 구조 제안

```php
// config/admin/setting.php
return [
    'middleware' => [
        // 전역 적용 미들웨어
        'global' => [
            'enabled' => true,
            'apply_to' => ['admin'],  // admin 미들웨어를 사용하는 모든 라우트
            'middlewares' => [
                'ip.whitelist' => [
                    'enabled' => env('ADMIN_IP_WHITELIST', false),
                    'mode' => 'strict',  // 'strict' 또는 'log_only'
                ],
                'check.password.change' => [
                    'enabled' => env('ADMIN_PASSWORD_CHECK', true),
                ],
            ],
        ],
        
        // 특정 컨트롤러/액션별 미들웨어
        'specific' => [
            'login' => [
                'captcha' => [
                    'enabled' => env('ADMIN_LOGIN_CAPTCHA', true),
                    'after_attempts' => 3,
                ],
            ],
            'sensitive_actions' => [
                'captcha' => [
                    'enabled' => env('ADMIN_ACTION_CAPTCHA', false),
                    'actions' => ['delete', 'massDelete', 'restore'],
                ],
            ],
        ],
    ],
];
```

## 커스텀 미들웨어 생성

필요한 경우 기본 미들웨어를 확장하여 커스텀 미들웨어를 만들 수 있습니다:

```php
<?php

namespace App\Http\Middleware;

use Jiny\Admin\App\Http\Middleware\AdminMiddleware;

class CustomAdminMiddleware extends AdminMiddleware
{
    protected function validateAdminAccess($request, $user, $requiredType = null)
    {
        // 부모 클래스의 검증 먼저 수행
        $result = parent::validateAdminAccess($request, $user, $requiredType);
        if ($result !== null) {
            return $result;
        }
        
        // 추가 커스텀 검증
        if ($this->needsAdditionalVerification($user)) {
            // 추가 검증 로직
        }
        
        return null;
    }
}
```

## 문제 해결

### 미들웨어가 적용되지 않는 경우

1. 서비스 프로바이더에서 미들웨어가 등록되었는지 확인
2. 라우트 캐시 클리어: `php artisan route:clear`
3. 설정 캐시 클리어: `php artisan config:clear`

### IP 화이트리스트 캐시 문제

```php
// 캐시 수동 클리어
\Jiny\Admin\App\Http\Middleware\IpWhitelistMiddleware::clearCache();
```

### CAPTCHA가 항상 요구되는 경우

설정에서 모드를 확인하세요:
- `always`: 항상 CAPTCHA 요구
- `smart`: 조건에 따라 CAPTCHA 요구

## 보안 권장사항

1. **프로덕션 환경**에서는 IP 화이트리스트 사용 권장
2. **민감한 작업**에는 CAPTCHA 적용
3. **비밀번호 만료** 정책 활성화
4. **로그 모니터링** 정기적 수행
5. **미들웨어 순서** 준수

## 성능 고려사항

1. **IP 화이트리스트**: 캐시 사용으로 DB 부하 감소
2. **CAPTCHA**: 필요한 경우에만 선택적 적용
3. **활동 시간 추적**: 1분 단위로 DB 업데이트 제한

---

*최종 업데이트: 2025-09-12*