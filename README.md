# @jiny/admin

Laravel 기반 Admin CRUD 시스템을 빠르게 구축할 수 있는 패키지입니다.

## 목차
- [설치](#설치)
- [기능](#기능)
- [사용법](#사용법)
  - [Artisan 명령어](#artisan-명령어)
  - [Claude 커스텀 명령어](#claude-커스텀-명령어)
- [구조](#구조)
- [설정](#설정)

## 설치

```bash
composer require jiny/admin
```

Service Provider는 자동으로 등록됩니다.

## 기능

- ✅ CRUD 자동 생성 (Create, Read, Update, Delete)
- ✅ Livewire 기반 동적 UI
- ✅ 테이블 검색 및 정렬
- ✅ 페이지네이션
- ✅ 일괄 작업 지원
- ✅ 설정 가능한 JSON 구성
- ✅ 반응형 디자인
- ✅ 라우트 자동 등록

## 사용법

### Artisan 명령어

#### 1. Admin CRUD 생성
```bash
php artisan admin:make {module} {feature}

# 예시
php artisan admin:make shop product
```

생성되는 파일:
- Controllers (6개): 메인, Create, Edit, Delete, Show, JSON 설정
- Model (1개)
- Views (5개): table, create, edit, show, search
- Migration (1개)
- Routes 자동 등록

#### 2. Admin CRUD 제거
```bash
php artisan admin:remove {module} {feature}

# 강제 삭제 (확인 없이)
php artisan admin:remove {module} {feature} --force

# 예시
php artisan admin:remove shop product --force
```

삭제되는 항목:
- 모든 컨트롤러 파일
- 모델 파일
- 뷰 파일들
- 마이그레이션 파일
- 라우트 설정
- 데이터베이스 테이블 (확인 후)

#### 3. 라우트 추가/확인
```bash
php artisan admin:route-add {module} {feature}

# 예시
php artisan admin:route-add shop product
```

기능:
- 컨트롤러 존재 확인
- 라우트 중복 체크
- 누락된 라우트 자동 추가
- 라우트 정보 테이블 표시

### Claude 커스텀 명령어

Claude Code에서 slash 명령어로 더 쉽게 사용할 수 있습니다.

#### 사용 가능한 명령어

```bash
# Admin CRUD 생성
/admin-create {module} {feature}

# Admin CRUD 제거  
/admin-remove {module} {feature}

# 라우트 확인/추가
/admin-route {module} {feature}

# 모든 Admin 모듈 목록
/admin-list
```

#### 예시
```bash
/admin-create shop category
/admin-remove shop category
/admin-route shop category
/admin-list
```

## 구조

### 디렉토리 구조
```
jiny/{module}/
├── App/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Admin/
│   │           └── Admin{Feature}/
│   │               ├── Admin{Feature}.php
│   │               ├── Admin{Feature}Create.php
│   │               ├── Admin{Feature}Edit.php
│   │               ├── Admin{Feature}Delete.php
│   │               ├── Admin{Feature}Show.php
│   │               └── Admin{Feature}.json
│   └── Models/
│       └── Admin{Feature}.php
├── database/
│   └── migrations/
│       └── {timestamp}_create_admin_{features}_table.php
├── resources/
│   └── views/
│       └── admin/
│           └── admin_{feature}/
│               ├── table.blade.php
│               ├── create.blade.php
│               ├── edit.blade.php
│               ├── show.blade.php
│               └── search.blade.php
└── routes/
    └── admin.php
```

### 라우트 구조
```php
GET    /admin/{feature}          // 목록
GET    /admin/{feature}/create   // 생성 폼
GET    /admin/{feature}/{id}/edit // 수정 폼
GET    /admin/{feature}/{id}     // 상세 보기
DELETE /admin/{feature}/{id}     // 삭제
```

## 설정

### JSON 설정 파일

각 Admin 모듈은 `Admin{Feature}.json` 파일로 설정됩니다.

주요 설정 항목:
```json
{
  "title": "제목",
  "table": {
    "name": "테이블명",
    "model": "모델 경로"
  },
  "index": {
    "paging": 20,
    "searchable": ["필드1", "필드2"],
    "sortable": ["필드1", "필드3"]
  },
  "create": {
    "buttonText": "추가 버튼 텍스트"
  },
  "validation": {
    "rules": {
      "title": "required|string|max:255"
    }
  }
}
```

### Livewire 컴포넌트

자동 등록되는 Livewire 컴포넌트:
- `jiny-admin::admin-table`
- `jiny-admin::admin-create`
- `jiny-admin::admin-edit`
- `jiny-admin::admin-show`
- `jiny-admin::admin-search`
- `jiny-admin::admin-delete`

## 테스트 데이터

Seeder를 통한 테스트 데이터 생성:

```php
// database/seeders/Admin{Feature}Seeder.php
php artisan db:seed --class=Admin{Feature}Seeder
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
php artisan migrate:fresh
php artisan migrate
```

### Livewire 컴포넌트 오류
```bash
php artisan livewire:discover
php artisan view:clear
```

## 라이선스

이 패키지는 JinyPHP 프레임워크의 일부입니다.