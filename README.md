# @jiny/admin

Laravel 기반 Admin CRUD 시스템을 빠르게 구축할 수 있는 패키지입니다.

## 목차
- [개요](#개요)
- [설치](#설치)
- [주요 기능](#주요-기능)
- [사용법](#사용법)
  - [Artisan 명령어](#artisan-명령어)
  - [Hook 시스템](#hook-시스템)
- [아키텍처](#아키텍처)
- [컴포넌트](#컴포넌트)
- [설정](#설정)
- [문제 해결](#문제-해결)

## 개요

@jiny/admin은 Laravel과 Livewire 3를 기반으로 한 관리자 CRUD 시스템입니다. 반복적인 관리자 페이지 개발을 자동화하고, 일관된 UI/UX를 제공합니다.

### 핵심 철학
- **Single Action Controller**: AI 코드 분석 최적화를 위한 단일 액션 컨트롤러
- **Component 기반**: 재사용 가능한 Livewire 컴포넌트
- **Hook 시스템**: 커스텀 로직 주입을 위한 유연한 Hook
- **JSON 설정**: 동적 동작을 위한 JSON 기반 설정

## 설치

### Composer를 통한 설치
```bash
composer require jiny/admin
```

### Service Provider 등록
`bootstrap/providers.php`에 자동으로 등록됩니다:
```php
return [
    // ...
    Jiny\Admin\JinyAdminServiceProvider::class,
];
```

### 설정 파일 퍼블리시 (선택사항)
```bash
php artisan vendor:publish --provider="Jiny\Admin\JinyAdminServiceProvider"
```

## 주요 기능

- ✅ **CRUD 자동 생성**: Create, Read, Update, Delete 페이지 자동 생성
- ✅ **Livewire 3 기반**: 실시간 동적 UI 컴포넌트
- ✅ **검색 및 필터링**: 실시간 검색, 상태 필터, 정렬 기능
- ✅ **페이지네이션**: 커스터마이징 가능한 페이지네이션
- ✅ **일괄 작업**: 체크박스를 통한 일괄 삭제
- ✅ **Hook 시스템**: 커스텀 로직 주입 가능
- ✅ **JSON 설정**: 동적 설정 관리
- ✅ **반응형 디자인**: Tailwind CSS 기반 반응형 UI
- ✅ **삭제 확인**: 안전한 삭제를 위한 확인 메커니즘

## 사용법

### Artisan 명령어

#### 1. Admin CRUD 생성
```bash
php artisan admin:make {module} {feature}

# 예시
php artisan admin:make admin usertype
php artisan admin:make shop product
php artisan admin:make blog post
```

**생성되는 파일:**
```
jiny/{module}/
├── App/Http/Controllers/Admin/Admin{Feature}/
│   ├── Admin{Feature}.php          # 메인 컨트롤러
│   ├── Admin{Feature}Create.php    # 생성 컨트롤러
│   ├── Admin{Feature}Edit.php      # 수정 컨트롤러
│   ├── Admin{Feature}Delete.php    # 삭제 컨트롤러
│   ├── Admin{Feature}Show.php      # 상세보기 컨트롤러
│   └── Admin{Feature}.json         # JSON 설정
├── App/Models/Admin{Feature}.php   # Eloquent 모델
├── database/migrations/            # 마이그레이션 파일
└── resources/views/admin/admin_{feature}/
    ├── table.blade.php             # 목록 테이블
    ├── create.blade.php            # 생성 폼
    ├── edit.blade.php              # 수정 폼
    ├── show.blade.php              # 상세보기
    └── search.blade.php            # 검색 폼
```

#### 2. Admin CRUD 제거
```bash
php artisan admin:remove {module} {feature}

# 강제 삭제 (확인 없이)
php artisan admin:remove {module} {feature} --force

# 예시
php artisan admin:remove shop product --force
```

#### 3. 라우트 확인 및 추가
```bash
php artisan admin:route-add {module} {feature}

# 예시
php artisan admin:route-add admin usertype
```

### Hook 시스템

Hook을 통해 기본 동작을 커스터마이징할 수 있습니다.

#### 사용 가능한 Hook 메서드

**목록 (Index) Hook:**
```php
// 데이터 fetch 전
public function hookIndexing($wire)
{
    // 조건 설정 등
    return false; // 계속 진행
}

// 데이터 fetch 후
public function hookIndexed($wire, $rows)
{
    // 데이터 가공
    return $rows;
}
```

**생성 (Create) Hook:**
```php
// 생성 폼 표시 전
public function hookCreating($wire, $value)
{
    // 초기값 설정
    return $form;
}

// DB 저장 전
public function hookStoring($wire, $form)
{
    // 데이터 검증/가공
    return $form;
}

// DB 저장 후
public function hookStored($wire, $form)
{
    // 후처리 작업
}
```

**수정 (Update) Hook:**
```php
// 수정 폼 표시 전
public function hookEditing($wire, $form)
{
    return $form;
}

// DB 업데이트 전
public function hookUpdating($wire, $form, $old)
{
    return $form;
}

// DB 업데이트 후
public function hookUpdated($wire, $form, $old)
{
    // 후처리 작업
}
```

**삭제 (Delete) Hook:**
```php
// 삭제 전
public function hookDeleting($wire, $row)
{
    return $row; // or false to cancel
}

// 삭제 후
public function hookDeleted($wire, $row)
{
    // 후처리 작업
}
```

## 아키텍처

### 컨트롤러 구조
Single Action Controller 패턴을 사용하여 각 액션별로 독립된 컨트롤러를 생성합니다:
- 코드 복잡도 감소
- AI 분석 최적화 (토큰 수 절감)
- 유지보수 용이성

### Livewire 컴포넌트
재사용 가능한 Livewire 3 컴포넌트:
- `AdminTable`: 목록 테이블 관리
- `AdminSearch`: 검색 및 필터링
- `AdminCreate`: 생성 폼 처리
- `AdminEdit`: 수정 폼 처리
- `AdminShow`: 상세보기
- `AdminDelete`: 삭제 확인 및 처리

### 이벤트 통신
컴포넌트 간 통신은 Livewire 이벤트를 통해 처리:
```php
// 검색 컴포넌트에서 이벤트 발생
$this->dispatch('search-updated', search: $value);

// 테이블 컴포넌트에서 이벤트 수신
#[On('search-updated')]
public function updateSearch($search) { }
```

## 컴포넌트

### AdminTable
목록 테이블을 관리하는 핵심 컴포넌트

**기능:**
- 페이지네이션
- 정렬 (컬럼 클릭)
- 체크박스 선택
- 일괄 삭제
- 로딩 시간 표시

### AdminSearch
검색 및 필터링 담당 컴포넌트

**기능:**
- 실시간 검색 (debounce 300ms)
- 상태 필터링
- 정렬 옵션
- 페이지당 개수 선택
- 검색 조건 표시

### AdminDelete
안전한 삭제를 위한 확인 컴포넌트

**기능:**
- 삭제 확인 모달
- 난수 입력 확인 (실수 방지)
- 복사 버튼 제공
- 단일/다중 삭제 지원

## 설정

### JSON 설정 파일
각 Admin 모듈은 `Admin{Feature}.json` 파일로 설정됩니다.

```json
{
  "title": "관리자 회원 등급",
  "subtitle": "관리자 권한 등급을 관리합니다",
  
  "route": {
    "name": "admin.user.type",
    "prefix": "admin.user.type"
  },
  
  "table": {
    "name": "admin_user_type",
    "model": "\\Jiny\\Admin\\App\\Models\\AdminUsertype",
    "primaryKey": "id",
    "timestamps": true,
    "casts": {
      "enable": "boolean"
    }
  },
  
  "index": {
    "tablePath": "jiny-admin::admin.admin_usertype.table",
    "searchPath": "jiny-admin::admin.admin_usertype.search",
    "pagination": {
      "perPage": "10",
      "perPageOptions": [10, 25, 50, 100]
    },
    "searchable": ["code", "name", "description"],
    "sortable": ["id", "code", "name", "level", "pos", "created_at"],
    "features": {
      "enableCreate": true,
      "enableDelete": true,
      "enableEdit": true,
      "enableSearch": true
    }
  },
  
  "create": {
    "formPath": "jiny-admin::admin.admin_usertype.create",
    "fillable": ["code", "name", "description", "level", "enable", "pos"]
  },
  
  "edit": {
    "formPath": "jiny-admin::admin.admin_usertype.edit",
    "fillable": ["name", "description", "level", "enable", "pos"]
  },
  
  "validation": {
    "rules": {
      "code": "required|string|max:50|unique:admin_user_type,code",
      "name": "required|string|max:255",
      "level": "required|integer|min:0|max:100"
    }
  }
}
```

### 라우트 구조
자동 생성되는 라우트:
```php
Route::prefix('admin')->group(function () {
    Route::get('/{feature}', [Admin{Feature}::class, '__invoke']);
    Route::get('/{feature}/create', [Admin{Feature}Create::class, '__invoke']);
    Route::get('/{feature}/{id}/edit', [Admin{Feature}Edit::class, '__invoke']);
    Route::get('/{feature}/{id}', [Admin{Feature}Show::class, '__invoke']);
    Route::delete('/{feature}/{id}', [Admin{Feature}Delete::class, '__invoke']);
});
```

## 실제 사용 예시

### 관리자 회원 등급 시스템
```bash
# 1. CRUD 생성
php artisan admin:make admin usertype

# 2. 마이그레이션 실행
php artisan migrate

# 3. 서버 실행
php artisan serve

# 4. 접속
http://localhost:8000/admin/user/type
```

### Hook 커스터마이징 예시
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

## 문제 해결

### 라우트가 작동하지 않을 때
```bash
php artisan route:clear
php artisan config:clear
php artisan admin:route-add {module} {feature}
```

### 마이그레이션 오류
```bash
php artisan migrate:rollback
php artisan migrate
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

## 기여하기

버그 리포트나 기능 제안은 GitHub Issues를 통해 제출해 주세요.

## 라이선스

이 패키지는 JinyPHP 프레임워크의 일부입니다.

## 변경 이력

### v1.0.0 (2025-09-01)
- 초기 릴리스
- 기본 CRUD 기능
- Hook 시스템
- Livewire 3 통합

### v1.1.0 (개발중)
- 페이지네이션 개선
- 검색 기능 강화
- 다국어 지원 추가 예정