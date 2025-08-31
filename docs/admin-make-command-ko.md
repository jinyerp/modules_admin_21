# Admin:Make 명령어 문서

## 개요
`admin:make` 명령어는 완전한 CRUD(생성, 조회, 수정, 삭제) 관리자 패널 기능을 생성하는 강력한 아티즌 명령어입니다. 컨트롤러, 뷰, 라우트, 설정 파일을 한 번에 생성합니다.

## 명령어 문법

```bash
php artisan admin:make {module} {feature} {table} [--model={model}]
```

### 매개변수

- **module** (필수): 관리자 기능이 생성될 모듈 이름 (예: `Admin`, `Shop`, `Site`)
- **feature** (필수): 관리자 패널의 기능 이름 (예: `Product`, `Category`, `User`)
- **table** (필수): 데이터베이스 테이블 이름 (예: `products`, `categories`, `users`)
- **--model** (선택): 모델 클래스 이름. 제공하지 않으면 feature 이름을 기본값으로 사용

### 사용 예시

```bash
# 기본 사용법
php artisan admin:make Admin Product products

# 커스텀 모델 이름 지정
php artisan admin:make Shop Category shop_categories --model=ShopCategory

# 다른 예시
php artisan admin:make Site Article articles --model=Article
```

## 생성되는 파일 구조

명령어를 실행하면 다음과 같은 파일 구조가 생성됩니다:

```
jiny/{module}/
├── App/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Admin/
│   │           └── Admin{Feature}/
│   │               ├── Admin{Feature}.php           # 메인 목록 컨트롤러
│   │               ├── Admin{Feature}Create.php     # 생성 컨트롤러
│   │               ├── Admin{Feature}Edit.php       # 수정 컨트롤러
│   │               ├── Admin{Feature}Show.php       # 상세보기 컨트롤러
│   │               ├── Admin{Feature}Delete.php     # 삭제 컨트롤러
│   │               └── Admin{Feature}.json          # 설정 파일
│   └── Models/
│       └── {Model}.php                              # Eloquent 모델
├── resources/
│   └── views/
│       └── admin/
│           └── {feature}/
│               ├── table.blade.php                  # 테이블 뷰
│               ├── create.blade.php                 # 생성 폼
│               ├── edit.blade.php                   # 수정 폼
│               ├── show.blade.php                   # 상세 뷰
│               └── search.blade.php                 # 검색 폼
└── routes/
    └── admin_{feature}.php                          # 라우트 정의
```

## 컨트롤러 기능

### Admin{Feature}.php (인덱스 컨트롤러)
- 목록 조회 및 페이지네이션 처리
- 검색 및 필터링 관리
- 커스터마이징을 위한 훅 제공:
  - `hookIndexing()`: 데이터 쿼리 전 실행
  - `hookIndexed()`: 데이터 조회 후 실행
  - `hookTableHeader()`: 테이블 헤더 커스터마이징
  - `hookPagination()`: 페이지네이션 설정
  - `hookSorting()`: 정렬 옵션 설정
  - `hookSearch()`: 검색 설정
  - `hookFilters()`: 필터 설정

### Admin{Feature}Create.php
- 생성 폼 표시
- 폼 제출 처리
- 제공되는 훅:
  - `hookCreating()`: 생성 폼 표시 전
  - `hookStoring()`: 데이터베이스 저장 전
  - `hookStored()`: 저장 성공 후

### Admin{Feature}Edit.php
- 기존 데이터와 함께 수정 폼 표시
- 레코드 업데이트
- 제공되는 훅:
  - `hookEditing()`: 수정 폼 표시 전
  - `hookUpdating()`: 데이터베이스 업데이트 전
  - `hookUpdated()`: 업데이트 성공 후

### Admin{Feature}Show.php
- 단일 레코드의 상세 뷰 표시
- 표시용 데이터 포맷팅
- 제공되는 훅:
  - `hookShowing()`: 데이터 표시 전
  - `hookShowed()`: 데이터 준비 후

### Admin{Feature}Delete.php
- 레코드 삭제 처리
- 소프트 삭제 지원 (설정 가능)
- 안전한 삭제를 위한 트랜잭션 지원

## JSON 설정 파일

`Admin{Feature}.json` 파일은 관리자 기능의 중앙 설정 파일입니다:

### 구조

```json
{
    "title": "기능 관리",
    "subtitle": "시스템의 기능 관리",
    "route": {
        "name": "admin.feature",
        "prefix": "admin.feature"
    },
    "table": {
        "name": "table_name",
        "model": "\\Namespace\\Model",
        "timestamps": true,
        "softDeletes": false
    },
    "template": {
        "layout": "jiny-admin::layouts.admin",
        "index": "jiny-admin::template.index",
        "create": "jiny-admin::template.create",
        "edit": "jiny-admin::template.edit",
        "show": "jiny-admin::template.show"
    },
    "index": {
        "paging": 20,
        "searchable": ["title", "description"],
        "sortable": ["id", "title", "created_at"],
        "features": {
            "enableCreate": true,
            "enableDelete": true,
            "enableEdit": true,
            "enableSearch": true,
            "enableSettingsDrawer": true
        }
    },
    "create": {
        "enableContinueCreate": true,
        "defaults": {
            "enable": true,
            "pos": 0
        }
    },
    "validation": {
        "rules": {
            "title": "required|string|max:255",
            "description": "nullable|string"
        }
    }
}
```

### 주요 설정 옵션

#### 테이블 설정
- `name`: 데이터베이스 테이블 이름
- `model`: Eloquent 모델의 전체 네임스페이스 경로
- `timestamps`: 자동 타임스탬프 활성화/비활성화
- `softDeletes`: 소프트 삭제 활성화/비활성화

#### 인덱스/목록 기능
- `paging`: 페이지당 항목 수
- `searchable`: 검색 가능한 필드
- `sortable`: 정렬 가능한 필드
- `features`: 다양한 UI 기능 토글

#### 폼 설정
- `defaults`: 새 레코드의 기본값
- `validation`: Laravel 유효성 검사 규칙
- `fillable`: 대량 할당 가능한 필드

## 라우트 등록

라우트는 자동으로 생성되며 다음 패턴을 따릅니다:

```php
Route::prefix('admin/{feature}')->name('admin.{feature}.')->group(function () {
    Route::get('/', Admin{Feature}::class)->name('index');
    Route::get('/create', Admin{Feature}Create::class)->name('create');
    Route::post('/store', Admin{Feature}Store::class)->name('store');
    Route::get('/{id}', Admin{Feature}Show::class)->name('show');
    Route::get('/{id}/edit', Admin{Feature}Edit::class)->name('edit');
    Route::put('/{id}', Admin{Feature}Update::class)->name('update');
    Route::delete('/{id}', Admin{Feature}Delete::class)->name('destroy');
});
```

## 뷰와 템플릿

### 기본 템플릿
명령어는 `jiny-admin`의 사전 정의된 템플릿을 사용합니다:
- `jiny-admin::template.index` - 목록 뷰 템플릿
- `jiny-admin::template.create` - 생성 폼 템플릿
- `jiny-admin::template.edit` - 수정 폼 템플릿
- `jiny-admin::template.show` - 상세 뷰 템플릿

### 커스텀 뷰
`resources/views/admin/{feature}/`에 생성되는 블레이드 파일:
- `table.blade.php`: 커스텀 테이블 레이아웃
- `create.blade.php`: 생성 폼 필드
- `edit.blade.php`: 수정 폼 필드
- `show.blade.php`: 상세 표시 레이아웃
- `search.blade.php`: 검색 폼 입력

## 설정 드로어 (Settings Drawer)

설정 드로어는 다음을 실시간으로 구성할 수 있는 슬라이드 아웃 패널입니다:
- 테이블 표시 옵션
- 폼 레이아웃
- 기능 토글
- 표시 형식

JSON 설정에서 `enableSettingsDrawer`를 `true`로 설정하면 자동으로 포함됩니다.

## 커스터마이징 훅

모든 컨트롤러는 핵심 코드 수정 없이 커스터마이징할 수 있는 훅을 제공합니다:

```php
// 예시: 저장 전 데이터 커스터마이징
public function hookStoring($wire, $form)
{
    $form['slug'] = Str::slug($form['title']);
    $form['user_id'] = auth()->id();
    return $form;
}

// 예시: 표시용 데이터 포맷팅
public function hookShowing($wire, $data)
{
    $data['created_at_formatted'] = Carbon::parse($data['created_at'])->format('Y년 m월 d일');
    return $data;
}
```

## 모범 사례

1. **훅 사용**: 생성된 컨트롤러를 수정하는 대신 훅을 사용하여 커스터마이징
2. **JSON 설정**: 쉬운 관리를 위해 모든 설정을 JSON 파일에 보관
3. **유효성 검사 규칙**: JSON 설정에 포괄적인 유효성 검사 규칙 정의
4. **명명 규칙**: 일관성을 위해 Admin{Feature} 명명 패턴 따르기
5. **데이터베이스 마이그레이션**: 명령어 실행 전에 데이터베이스 마이그레이션 생성

## 문제 해결

### 일반적인 문제

1. **빈 페이지 오류**
   - 모든 필수 변수가 뷰에 전달되는지 확인
   - JSON 설정 경로가 올바른지 확인
   - Livewire 컴포넌트가 단일 루트 요소를 가지는지 확인

2. **라우트를 찾을 수 없음**
   - 생성된 라우트 파일을 서비스 프로바이더에 등록
   - 라우트 캐시 지우기: `php artisan route:clear`

3. **모델 누락**
   - 모델이 존재하는지 확인하거나 `--model` 매개변수 사용
   - JSON 설정의 네임스페이스 확인

4. **권한 문제**
   - 모듈 디렉토리에 대한 쓰기 권한 확인
   - 생성 후 파일 소유권 확인

## 고급 기능

### 다단계 계층 데이터
생성된 컨트롤러는 트리 구조를 위한 `pos`, `depth`, `ref` 필드로 계층적 데이터를 지원합니다.

### 대량 작업
여러 레코드 작업을 허용하려면 JSON 설정에서 대량 작업을 활성화합니다.

### 내보내기/가져오기
데이터 이동성을 위해 JSON에서 내보내기 및 가져오기 기능을 구성합니다.

### 커스텀 Livewire 컴포넌트
JSON 설정의 경로를 업데이트하여 기본 Livewire 컴포넌트를 교체합니다.

## 주요 개선사항 (AdminTest 기반)

### 1. 설정 경로 변수 추가
모든 컨트롤러에 `$settingsPath` 변수가 추가되어 설정 드로어가 정상 작동합니다.

### 2. JSON 키 접근 오류 수정
뷰 파일에서 존재하지 않는 JSON 키에 접근하는 문제를 해결했습니다.

### 3. 단순화된 기본 구조
불필요한 필드를 제거하고 필수 기능에 집중하여 초기 설정을 간소화했습니다.

### 4. 향상된 오류 처리
JSON 파일 로드 실패 시 명확한 오류 메시지를 제공합니다.

## 결론

`admin:make` 명령어는 단일 명령으로 필요한 모든 파일을 생성하여 관리자 패널 생성을 간소화합니다. 생성된 코드는 Laravel 모범 사례를 따르며 훅과 JSON 설정을 통해 광범위한 커스터마이징 옵션을 제공합니다.