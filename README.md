# @jiny/admin - Laravel Admin CRUD Generator

## 📋 개요

@jiny/admin은 Laravel 11/12를 위한 포괄적인 관리자 패널 시스템입니다. Livewire 3를 활용하여 동적인 CRUD 인터페이스를 자동으로 생성하며, Hook 시스템을 통해 유연한 커스터마이징이 가능합니다.

## ✅ 제품 요구 사항 및 구현 현황

### 1. 핵심 기능 (Core Features)

#### 1.1 CRUD 자동 생성
- [x] **관리자 모듈 생성 명령어** (`php artisan admin:make`)
  - [x] 6개 컨트롤러 자동 생성 (메인, Create, Edit, Delete, Show, JSON 설정)
  - [x] 모델 파일 생성
  - [x] 마이그레이션 파일 생성
  - [x] 뷰 템플릿 생성 (table, create, edit, show, search)
  - [x] 라우트 자동 등록

- [x] **관리자 모듈 제거 명령어** (`php artisan admin:remove`)
  - [x] 생성된 파일 안전 제거
  - [x] --force 옵션 지원

- [x] **라우트 추가 명령어** (`php artisan admin:route-add`)
  - [x] 라우트 파일 자동 업데이트

#### 1.2 Livewire 컴포넌트
- [x] **AdminTable** - 데이터 테이블 컴포넌트
  - [x] 페이지네이션
  - [x] 정렬 기능
  - [x] 검색 기능
  - [x] 일괄 선택/삭제

- [x] **AdminCreate** - 데이터 생성 컴포넌트
  - [x] 폼 유효성 검증
  - [x] 파일 업로드 지원

- [x] **AdminEdit** - 데이터 수정 컴포넌트
  - [x] 기존 데이터 로드
  - [x] 변경 사항 추적

- [x] **AdminDelete** - 삭제 확인 컴포넌트
  - [x] 소프트 삭제 지원
  - [x] 삭제 확인 모달

- [x] **AdminShow** - 상세 보기 컴포넌트
  - [x] 읽기 전용 뷰
  - [x] 관련 데이터 표시

- [x] **AdminSearch** - 검색 컴포넌트
  - [x] 다중 필드 검색
  - [x] 필터링 옵션

- [x] **AdminHeaderWithSettings** - 헤더 및 설정
  - [x] 설정 드로어
  - [x] 사용자 프로필

### 2. 인증 및 보안 (Authentication & Security)

#### 2.1 로그인 시스템
- [x] **기본 로그인** (`AdminAuth.php`)
  - [x] 이메일/비밀번호 인증
  - [x] Remember Me 기능
  - [x] 세션 관리

- [x] **로그인 페이지** (`AdminLogin.php`)
  - [x] JSON 설정 파일 지원
  - [x] 커스터마이징 가능한 뷰
  - [x] 자동 리다이렉트

- [x] **로그아웃** (`AdminLogout.php`)
  - [x] 세션 종료
  - [x] 활동 로그 기록

#### 2.2 비밀번호 보안
- [x] **비밀번호 정책** (`AdminPasswordChange.php`)
  - [x] 최소 길이 (8자)
  - [x] 대소문자 혼합 필수
  - [x] 숫자 포함 필수
  - [x] 특수문자 포함 필수
  - [x] 유출된 비밀번호 체크

- [x] **비밀번호 히스토리**
  - [x] 이전 비밀번호 재사용 방지 (최근 3개)
  - [x] 비밀번호 변경 로그 (`AdminPasswordLog`)

- [x] **비밀번호 만료**
  - [x] 만료 기간 설정 (기본 90일)
  - [x] 강제 변경 플래그
  - [x] 만료 알림

- [x] **계정 잠금**
  - [x] 로그인 실패 횟수 제한 (5회)
  - [x] IP 기반 차단
  - [x] 자동 잠금 해제 (30분)
  - [x] 수동 잠금 해제 명령어

#### 2.3 2단계 인증 (2FA)
- [x] **Google Authenticator 연동** (`Admin2FA.php`)
  - [x] TOTP 코드 생성
  - [x] QR 코드 표시
  - [x] 백업 코드 (8개)

- [x] **2FA 관리**
  - [x] 활성화/비활성화
  - [x] 백업 코드 재생성
  - [x] 시도 횟수 제한 (5회)

### 3. 사용자 관리 (User Management)

#### 3.1 사용자 CRUD
- [x] **사용자 관리** (`AdminUsers`)
  - [x] 사용자 목록 조회
  - [x] 사용자 생성/수정/삭제
  - [x] 권한 할당
  - [x] 활성화/비활성화

#### 3.2 사용자 타입
- [x] **사용자 타입 관리** (`AdminUsertype`)
  - [x] 타입 생성/수정/삭제
  - [x] 권한 레벨 설정
  - [x] 사용자 수 추적

#### 3.3 사용자 활동 추적
- [x] **활동 로그** (`AdminUserLog`)
  - [x] 로그인/로그아웃 기록
  - [x] 비밀번호 변경 기록
  - [x] 관리 작업 기록
  - [x] IP 주소 및 User Agent 저장

- [x] **세션 관리** (`AdminUserSession`)
  - [x] 활성 세션 추적
  - [x] 동시 로그인 제어
  - [x] 세션 만료 관리
  - [x] 강제 로그아웃

### 4. 모니터링 및 로깅 (Monitoring & Logging)

#### 4.1 대시보드
- [x] **관리자 대시보드** (`AdminDashboard`)
  - [x] 통계 위젯
  - [x] 최근 활동
  - [x] 시스템 상태

- [x] **통계 페이지** (`AdminStats`)
  - [x] 사용자 통계
  - [x] 활동 분석
  - [x] 보안 이벤트

#### 4.2 로그 관리
- [x] **사용자 로그** (`AdminUserLogs`)
  - [x] 로그 조회
  - [x] 필터링 및 검색
  - [x] 로그 상세 보기
  - [x] 로그 삭제 (관리자만)

- [x] **비밀번호 로그** (`AdminPasswordLogs`)
  - [x] 실패 시도 추적
  - [x] IP 차단 관리
  - [x] 차단 해제 기능

### 5. UI/UX 기능 (User Interface)

#### 5.1 레이아웃
- [x] **반응형 디자인**
  - [x] 모바일 지원
  - [x] 태블릿 지원
  - [x] 데스크톱 최적화

- [ ] **다크 모드**
  - [ ] 다크 테마 지원
  - [ ] 자동 전환
  - [ ] 사용자 설정 저장

#### 5.2 네비게이션
- [x] **사이드바**
  - [x] 계층형 메뉴
  - [x] 접기/펼치기
  - [x] 활성 메뉴 표시

- [x] **헤더**
  - [x] 사용자 프로필
  - [x] 알림
  - [x] 빠른 작업

#### 5.3 알림 시스템
- [x] **Toast 알림**
  - [x] 성공/에러/정보 메시지
  - [x] 자동 사라짐
  - [x] 액션 버튼

### 6. 개발자 도구 (Developer Tools)

#### 6.1 설정 관리
- [x] **JSON 기반 설정**
  - [x] 각 모듈별 JSON 설정 파일
  - [x] 런타임 설정 변경
  - [x] 기본값 지원

- [x] **중앙 설정 파일** (`config/setting.php`)
  - [x] 비밀번호 정책
  - [x] 페이지네이션
  - [x] 날짜/시간 형식
  - [x] 파일 업로드 제한

#### 6.2 Hook 시스템
- [x] **Hook 지원** (`Trait/Hook.php`)
  - [x] 이벤트 기반 확장
  - [x] Before/After 훅
  - [x] 커스텀 액션

#### 6.3 헬퍼 함수
- [x] **유틸리티 함수** (`Helpers/Helper.php`)
  - [x] 경로 헬퍼
  - [x] 설정 헬퍼
  - [x] 권한 체크

### 7. 테스트 (Testing)

- [x] **단위 테스트**
  - [x] 컨트롤러 테스트 (`AdminHelloControllerTest`)
  - [x] 모델 테스트

- [x] **기능 테스트**
  - [x] CRUD 동작 테스트 (`AdminHelloTest`)
  - [x] 인증 테스트

### 8. 문서화 (Documentation)

- [x] **개발 문서**
  - [x] admin-make 명령어 가이드 (한글/영문)
  - [x] 비밀번호 보안 가이드 (`PASSWORD_SECURITY.md`)
  - [x] 2FA 설정 가이드 (`TWO_FACTOR_AUTH.md`)
  - [x] Hook 시스템 가이드 (`hook.md`)
  - [x] 사용 방법 (`howto.md`)

### 9. 데이터베이스 (Database)

#### 9.1 마이그레이션
- [x] 관리자 테이블 마이그레이션
- [x] 사용자 확장 필드
- [x] 로그 테이블
- [x] 세션 테이블

#### 9.2 시더
- [x] 샘플 데이터 시더 (`AdminTemplateSeeder`)
- [ ] 초기 관리자 계정

#### 9.3 팩토리
- [x] 테스트용 팩토리 (`AdminTemplateFactory`)

## 📊 구현 통계

- **총 요구사항**: 90개
- **구현 완료**: 86개 (95.6%)
- **미구현**: 4개 (4.4%)
  - 다크 모드 (테마 지원, 자동 전환, 설정 저장)
  - 초기 관리자 계정 시더

## 🚀 시작하기

### 요구사항
- PHP 8.2 이상
- Laravel 11.0 이상 또는 12.0
- Livewire 3.0 이상

### 설치
```bash
composer require jiny/admin
```

### Service Provider 등록
Laravel 11/12에서는 `bootstrap/providers.php`에 자동으로 등록됩니다.

수동으로 등록이 필요한 경우:
```php
return [
    // ...
    Jiny\Admin\JinyAdminServiceProvider::class,
];
```

### 마이그레이션 실행
```bash
php artisan migrate
```

### 설정 파일 퍼블리시 (선택사항)
```bash
php artisan vendor:publish --tag=admin-config
```

## 🎯 사용 예시

### Admin CRUD 생성
```bash
# 기본 명령어
php artisan admin:make {module} {feature}

# 예시
php artisan admin:make admin usertype
php artisan admin:make shop product
php artisan admin:make blog post

# 마이그레이션 실행
php artisan migrate

# 서버 실행 및 접속
php artisan serve
# http://localhost:8000/admin/usertype
```

### Hook 커스터마이징
```php
// AdminUsertype.php
public function hookIndexing($wire)
{
    // 관리자만 접근 가능
    if (!auth()->user()->isAdmin()) {
        return view("jiny-admin::error.unauthorized");
    }
}

public function hookStoring($wire, $form)
{
    // 코드 자동 생성
    if (empty($form['code'])) {
        $form['code'] = Str::slug($form['name']);
    }
    return $form;
}
```

## 🔧 문제 해결

### 라우트가 작동하지 않을 때
```bash
php artisan route:clear
php artisan config:clear
php artisan admin:route-add {module} {feature}
```

### Livewire 컴포넌트 오류
```bash
php artisan livewire:discover
php artisan view:clear
php artisan cache:clear
```

### Class not found 오류
```bash
composer dump-autoload
php artisan optimize:clear
```

## 🛠 향후 개발 계획

1. **다크 모드 완성**
   - Tailwind CSS 다크 모드 클래스 적용
   - 시스템 설정 연동
   - 사용자별 테마 저장

2. **고급 보안 기능**
   - IP 화이트리스트
   - 지리적 위치 기반 차단
   - 이상 행동 감지

3. **성능 최적화**
   - 캐싱 전략 개선
   - 쿼리 최적화
   - 지연 로딩

4. **국제화 (i18n)**
   - 다국어 지원
   - 타임존 처리
   - 통화 및 날짜 형식

5. **API 확장**
   - RESTful API 엔드포인트
   - GraphQL 지원
   - API 문서 자동 생성

## 📝 라이선스

MIT License

## 👥 기여자

JinyPHP Team

## 🔄 변경 이력

### v1.0.0 (2025-09-05)
- 초기 공식 릴리스
- 완전한 CRUD 자동 생성 시스템
- Livewire 3 컴포넌트 통합
- Hook 시스템을 통한 유연한 커스터마이징
- 포괄적인 인증 및 보안 기능
  - 비밀번호 보안 정책
  - 2단계 인증 (2FA)
  - 세션 관리 및 활동 로깅
- 사용자 및 권한 관리
- 반응형 관리자 대시보드
- JSON 기반 설정 시스템

---
*최종 업데이트: 2025.09.05*