# Jiny Admin 문서

Laravel용 Jiny Admin 패키지의 완전한 문서 - 자동 CRUD 생성 기능을 갖춘 종합 관리자 패널 시스템

## 목차

### 시작하기
- [설치 가이드](#설치-가이드)
- [설정 가이드](CONFIG_GUIDE.md)
- [빠른 시작 튜토리얼](controller/howto.md)
- [Tailwind CSS & Vite 설정](features/TAILWIND_VITE_SETUP.md)

### 핵심 개념
- [관리자 명령어](controller/명령어.md)
- [Hook 시스템](controller/hook.md)
- [컨트롤러 아키텍처](controller/_컨트롤러.md)
  - [컨트롤러 구조](controller/_컨트롤러.md)
  - [JSON 설정](controller/컨트롤러_json.md)
  - [SAC 패턴](controller/컨틀롤러_sac.md)

### 기능별 문서

#### 1. 관리자 페이지 관리 (Admin Page Management)
Jiny Admin의 핵심 기능으로, 자동 CRUD 생성을 통한 관리자 페이지 시스템

##### 컨트롤러 & 컴포넌트
- [컨트롤러 구조](controller/_컨트롤러.md) - SAC 패턴 기반 컨트롤러 시스템
- [JSON 설정](controller/컨트롤러_json.md) - 컨트롤러 동작 설정
- [리소스 컨트롤러](controller/컨트롤러_리소스.md) - CRUD 컨트롤러 구조
- [Hook 시스템](controller/hook.md) - 컨트롤러 확장 메커니즘

##### 뷰 & 템플릿
- [관리자 페이지](controller/관리자_페이지.md) - 페이지 구조와 템플릿
- [Livewire 컴포넌트 활용](controller/howto.md) - 동적 UI 구현

##### 명령어 & 생성도구
- [Admin 명령어](controller/명령어.md) - CRUD 자동 생성 명령어

#### 2. 관리자 제어 페이지 (/admin/* Control Pages)
시스템 관리를 위한 내장 관리 페이지들

##### 사용자 관리
- [사용자 관리](features/AdminUsers.md) - 관리자 계정 관리
- [사용자 유형](features/AdminUsertype.md) - 권한 그룹 관리
- [사용자 활동 로그](features/AdminUserLogs.md) - 활동 추적
- [세션 관리](features/AdminSessions.md) - 활성 세션 모니터링

##### 보안 관리
- [비밀번호 관리](features/AdminUserPassword.md) - 비밀번호 정책
- [비밀번호 로그](features/AdminPasswordLogs.md) - 로그인 시도 추적
- [2단계 인증 (2FA)](features/AdminUser2fa.md) - TOTP 기반 보안
- [IP 화이트리스트](features/ip-whitelist.md) - 접근 IP 제어

##### 시스템 모니터링
- [관리자 대시보드](features/AdminDashboard.md) - 시스템 현황판
- [관리자 콘솔](controller/관리자_콘솔.md) - 시스템 제어
- [통계](features/AdminStats.md) - 사용 통계 및 분석

#### 3. 관리자 로그인 처리 (Admin Login Processing)
전용 관리자 인증 시스템

##### 인증 시스템
- [관리자 로그인](controller/관리자_로그인.md) - 분리된 로그인 시스템
- [인증 처리](controller/관리자_로그인.md#로그인-프로세스) - 로그인 플로우
- [세션 관리](features/AdminSessions.md) - 관리자 세션 처리

##### 보안 기능
- [자동 차단 시스템](controller/관리자_로그인.md#비밀번호-보안-관리) - 무차별 공격 방어
- [2FA 인증](controller/관리자_로그인.md#2fa-인증-시스템) - 추가 보안 계층
- [로그 추적](controller/관리자_로그인.md#로그-기록) - 모든 인증 활동 기록

#### 추가 보안 기능
- [CAPTCHA 시스템](features/captcha.md) - 자동화 공격 방어
  - [CAPTCHA 설정 가이드](features/CAPTCHA_SETUP.md)
  - [CAPTCHA 빠른 시작](features/captcha-quick-start.md)
  - [CAPTCHA 사용 가이드](features/CAPTCHA_사용_가이드.md)
  - [CAPTCHA 로그](features/captcha-logs.md)
  - [CAPTCHA 문제해결](features/captcha-troubleshooting.md)
  - [CAPTCHA 설정 지침](features/captcha-setup-guide.md)

#### 통신 기능
- [SMS 서비스](features/sms.md) - SMS 발송 및 관리

---

## 설치 가이드

### 시스템 요구사항

- PHP 8.2 이상
- Laravel 11.0+ 또는 12.0+
- Livewire 3.0+
- Tailwind CSS 4.0+
- Composer 2.0+

### Composer를 통한 설치

Jiny Admin 설치를 위한 권장 방법입니다:

```bash
composer require jinyerp/admin
```

설치 후 패키지는 자동으로 다음을 수행합니다:
1. 서비스 프로바이더 등록
2. 데이터베이스 마이그레이션 실행
3. 설정 파일 배포
4. 애셋 파일 배포

### 수동 설치

로컬 개발 또는 커스텀 설정을 위한 방법:

1. **패키지를 클론하거나 복사**하여 `jiny/admin` 디렉토리에 배치:
```bash
mkdir -p jiny
cp -r path/to/jiny-admin jiny/admin
```

2. **composer.json을 업데이트**하여 로컬 패키지 포함:
```json
{
    "autoload": {
        "psr-4": {
            "Jiny\\Admin\\": "jiny/admin/"
        }
    }
}
```

3. **오토로드 파일 재생성**:
```bash
composer dump-autoload
```

4. **서비스 프로바이더 등록** (Laravel 11/12):
`bootstrap/providers.php`에 추가:
```php
return [
    // 다른 프로바이더들...
    Jiny\Admin\JinyAdminServiceProvider::class,
];
```

5. **마이그레이션 실행**:
```bash
php artisan migrate
```

6. **애셋 배포** (선택사항):
```bash
php artisan vendor:publish --tag=jiny-admin-config
php artisan vendor:publish --tag=jiny-admin-assets
```

### 설치 후 설정

1. **Tailwind CSS 설정** 벤더 패키지용 설정:
   - [Tailwind CSS & Vite 설정 가이드](features/TAILWIND_VITE_SETUP.md) 참조

2. **인증 설정** (아직 설정되지 않은 경우):
```bash
php artisan make:auth
```

3. **관리자 계정 생성**:
```bash
php artisan admin:create
```

4. **관리자 패널 접속**:
```
http://your-domain.com/admin
```

---

## 빠른 시작

### 첫 번째 Admin 모듈 생성

1. **완전한 admin 모듈 생성**:
```bash
php artisan admin:make shop product
```

이 명령으로 생성되는 파일들:
- 6개 컨트롤러 (Main, Create, Edit, Delete, Show + JSON 설정)
- 모델 파일
- 마이그레이션 파일
- 5개 뷰 템플릿 (table, create, edit, show, search)
- 라우트 등록

2. **마이그레이션 실행**:
```bash
php artisan migrate
```

3. **새 모듈에 접속**:
```
http://your-domain.com/admin/product
```

### Hook을 사용한 커스터마이징

Hook 시스템을 통해 핵심 파일을 수정하지 않고도 동작을 커스터마이징할 수 있습니다:

```php
// 컨트롤러(예: AdminProduct.php)에서

public function hookIndexing($wire)
{
    // 인덱스 페이지 표시 전 커스텀 로직
    if (!auth()->user()->hasPermission('view_products')) {
        abort(403);
    }
}

public function hookStoring($wire, $form)
{
    // 저장 전 폼 데이터 수정
    $form['slug'] = Str::slug($form['name']);
    $form['created_by'] = auth()->id();
    return $form;
}

public function hookStored($wire, $form, $id)
{
    // 저장 후 액션
    Log::info("Product created: {$id}");
    
    // 알림 발송
    Notification::send(
        User::admins()->get(),
        new ProductCreated($id)
    );
}
```

### JSON 설정 작업

각 admin 모듈은 커스터마이징을 위한 JSON 설정 파일을 가집니다:

```json
{
    "title": "상품 관리",
    "table": {
        "columns": ["id", "name", "price", "status", "created_at"],
        "search": ["name", "description"],
        "sort": "created_at",
        "order": "desc",
        "perPage": 20
    },
    "form": {
        "fields": {
            "name": {
                "type": "text",
                "label": "상품명",
                "required": true,
                "validation": "required|string|max:255"
            },
            "price": {
                "type": "number",
                "label": "가격",
                "required": true,
                "validation": "required|numeric|min:0"
            },
            "status": {
                "type": "select",
                "label": "상태",
                "options": {
                    "active": "활성",
                    "inactive": "비활성"
                },
                "default": "active"
            }
        }
    },
    "permissions": {
        "view": "view_products",
        "create": "create_products",
        "edit": "edit_products",
        "delete": "delete_products"
    }
}
```

---

## 아키텍처 개요

### 디렉토리 구조

```
jiny/admin/
   App/
      Console/Commands/       # Artisan 명령어
      Http/
         Controllers/       # Admin 컨트롤러
            Admin/          # CRUD 관리 컨트롤러
            Web/            # 로그인 관련 컨트롤러
         Livewire/         # Livewire 컴포넌트
         Middleware/       # 커스텀 미들웨어
      Models/               # Eloquent 모델
      Services/             # 서비스 클래스
      Traits/               # 재사용 가능한 트레이트
   config/                   # 설정 파일
   database/
      migrations/          # 데이터베이스 마이그레이션
      factories/          # 모델 팩토리
      seeders/            # 데이터베이스 시더
   docs/                   # 문서
   resources/
      views/             # Blade 템플릿
   routes/                # 라우트 정의
   stubs/                 # 생성용 템플릿 파일
   tests/                 # 테스트 파일
```

### 컴포넌트 아키텍처

패키지는 다음 핵심 컴포넌트들로 구성된 모듈형 아키텍처를 사용합니다:

1. **컨트롤러**: HTTP 요청과 비즈니스 로직 처리
2. **Livewire 컴포넌트**: 반응형 UI 컴포넌트 제공
3. **모델**: 데이터 구조와 관계 정의
4. **서비스**: 비즈니스 로직 캡슐화
5. **트레이트**: 공통 기능 공유
6. **Hook**: 커스터마이징 지점 제공

### 데이터베이스 스키마

패키지에서 생성하는 주요 테이블:

- `users` - 관리자 필드로 확장
- `admin_user_types` - 사용자 역할 정의
- `admin_user_logs` - 활동 추적
- `admin_user_sessions` - 세션 관리
- `admin_password_logs` - 비밀번호 시도 추적
- `admin_user_2fas` - 2FA 설정
- `admin_ip_tracking` - IP 기반 보안
- `admin_ip_whitelist` - 허용 IP
- `admin_ip_blacklist` - 차단 IP

---

## 고급 주제

### 보안 기능

#### 비밀번호 보안
- 최소 8자
- 대소문자 혼합 요구
- 숫자 요구
- 특수문자 요구
- 비밀번호 기록 (최근 3개 재사용 방지)
- 비밀번호 만료 (기본 90일)
- 유출 탐지

#### 2단계 인증
- TOTP 지원 (Google Authenticator)
- SMS 기반 2FA
- 이메일 기반 2FA
- 백업 코드
- 복구 옵션

#### IP 보안
- IP 화이트리스트/블랙리스트
- 지리적 제한
- 속도 제한
- 의심스러운 활동 탐지

### 성능 최적화

1. **쿼리 최적화**
   - 관계 즉시 로딩
   - 쿼리 캐싱
   - 데이터베이스 인덱싱

2. **뷰 캐싱**
   - Blade 템플릿 캐싱
   - Livewire 컴포넌트 캐싱

3. **애셋 최적화**
   - 압축
   - CDN 통합
   - 지연 로딩

### 테스트

테스트 스위트 실행:

```bash
# 모든 테스트
php artisan test

# 특정 테스트 파일
php artisan test tests/Feature/AdminTest.php

# 커버리지와 함께
php artisan test --coverage
```

### 문제 해결

#### 일반적인 문제

1. **라우트가 작동하지 않음**
```bash
php artisan route:clear
php artisan config:clear
php artisan admin:route-add {module} {feature}
```

2. **Livewire 컴포넌트를 찾을 수 없음**
```bash
php artisan livewire:discover
php artisan view:clear
```

3. **클래스를 찾을 수 없는 오류**
```bash
composer dump-autoload
php artisan optimize:clear
```

4. **마이그레이션 오류**
```bash
php artisan migrate:fresh --seed
```

---

## API 참조

### Artisan 명령어

| 명령어 | 설명 |
|---------|-------------|
| `admin:make {module} {feature}` | 완전한 admin 모듈 생성 |
| `admin:make-controller {module} {controller}` | 컨트롤러만 생성 |
| `admin:make-view {module} {controller}` | 뷰만 생성 |
| `admin:make-json {module} {controller}` | JSON 설정만 생성 |
| `admin:remove {module} {feature}` | admin 모듈 제거 |
| `admin:route-add {module} {feature}` | 모듈에 라우트 추가 |
| `admin:create` | 관리자 계정 생성 |
| `admin:password-reset {email}` | 관리자 비밀번호 재설정 |

### Hook 메소드

| Hook | 설명 | 매개변수 |
|------|-------------|------------|
| `hookIndexing()` | 인덱스 페이지 표시 전 | `$wire` |
| `hookIndexed()` | 인덱스 페이지 표시 후 | `$wire` |
| `hookCreating()` | 생성 폼 표시 전 | `$wire` |
| `hookStoring()` | 데이터 저장 전 | `$wire, $form` |
| `hookStored()` | 데이터 저장 후 | `$wire, $form, $id` |
| `hookEditing()` | 수정 폼 표시 전 | `$wire, $id` |
| `hookUpdating()` | 데이터 업데이트 전 | `$wire, $form, $id` |
| `hookUpdated()` | 데이터 업데이트 후 | `$wire, $form, $id` |
| `hookDeleting()` | 데이터 삭제 전 | `$wire, $id` |
| `hookDeleted()` | 데이터 삭제 후 | `$wire, $id` |

### 헬퍼 함수

| 함수 | 설명 |
|----------|-------------|
| `admin_path()` | admin 패키지 경로 가져오기 |
| `admin_config()` | admin 설정 가져오기 |
| `admin_view()` | admin 뷰 경로 가져오기 |
| `admin_route()` | admin 라우트 생성 |

---

## 기여하기

기여를 환영합니다! 자세한 내용은 [기여 가이드](CONTRIBUTING.md)를 참조하세요.

### 개발 환경 설정

1. 저장소 포크
2. 포크된 저장소 클론
3. 기능 브랜치 생성
4. 변경사항 적용
5. 테스트 작성
6. 풀 리퀘스트 제출

### 코딩 표준

- PSR-12 코딩 표준 준수
- 의미 있는 변수명과 메소드명 사용
- 포괄적인 PHPDoc 주석 작성
- 새 기능에 대한 단위 테스트 포함

---

## 지원

### 도움 받기

- **문서**: 이 문서
- **이슈**: [GitHub Issues](https://github.com/jinyphp/admin/issues)
- **토론**: [GitHub Discussions](https://github.com/jinyphp/admin/discussions)
- **이메일**: infohojin@gmail.com

### 버그 신고

GitHub Issues를 통해 다음 정보와 함께 버그를 신고해 주세요:
- 명확한 설명
- 재현 단계
- 예상 vs 실제 동작
- 시스템 정보
- 오류 메시지/로그

### 기능 요청

GitHub Issues를 통해 다음 정보와 함께 기능 요청을 제출해 주세요:
- 사용 사례 설명
- 제안된 해결책
- 대안 고려사항

---

## 라이선스

Jiny Admin 패키지는 [MIT 라이선스](https://opensource.org/licenses/MIT) 하에 배포되는 오픈소스 소프트웨어입니다.

---

## 변경 로그

최근 변경사항은 [CHANGELOG.md](CHANGELOG.md)를 참조하세요.

---

*이 문서는 2025-09-12에 마지막으로 업데이트되었습니다.*