# Hook System Documentation

@jiny/admin의 Hook 시스템은 기본 CRUD 동작을 커스터마이징할 수 있는 유연한 확장 포인트를 제공합니다.

## 개요

Hook 시스템은 Controller와 Livewire 컴포넌트 사이의 브리지 역할을 합니다. Livewire 컴포넌트가 특정 시점에 Controller의 Hook 메서드를 호출하여 커스텀 로직을 실행할 수 있습니다.

## Hook 메서드 목록

### 1. 목록 페이지 (Index) Hooks

#### hookIndexing($wire)
**호출 시점**: 데이터를 fetch하기 전  
**용도**: 검색 조건 설정, 권한 체크, 필터 설정

```php
public function hookIndexing($wire)
{
    // 예시 1: 권한 체크
    if (!auth()->user()->can('view-list')) {
        return view("jiny-admin::error.unauthorized");
    }
    
    // 예시 2: 조건 설정
    $wire->actions['where'] = [
        'user_id' => auth()->id()
    ];
    
    // false 반환시 계속 진행
    return false;
}
```

**반환값**:
- `false` 또는 반환값 없음: 정상 진행
- View 반환: 해당 뷰를 표시하고 종료
- 기타 값: 동작 중단

#### hookIndexed($wire, $rows)
**호출 시점**: 데이터 fetch 후  
**용도**: 데이터 가공, 추가 정보 병합

```php
public function hookIndexed($wire, $rows)
{
    // 데이터 가공
    foreach ($rows as $row) {
        $row->formatted_date = Carbon::parse($row->created_at)->format('Y-m-d');
    }
    
    // 반드시 rows를 반환
    return $rows;
}
```

### 2. 생성 (Create) Hooks

#### hookCreating($wire, $value)
**호출 시점**: 생성 폼이 표시되기 전  
**용도**: 초기값 설정, 폼 데이터 준비

```php
public function hookCreating($wire, $value)
{
    $form = [];
    
    // 초기값 설정
    $form['user_id'] = auth()->id();
    $form['status'] = 'draft';
    $form['created_date'] = now();
    
    return $form; // 폼 초기값으로 설정됨
}
```

#### hookStoring($wire, $form)
**호출 시점**: DB에 저장하기 전  
**용도**: 데이터 검증, 변환, 추가 처리

```php
public function hookStoring($wire, $form)
{
    // 데이터 검증
    if (empty($form['title'])) {
        session()->flash('error', '제목은 필수입니다.');
        return false; // 저장 중단
    }
    
    // 데이터 변환
    $form['slug'] = Str::slug($form['title']);
    $form['user_id'] = auth()->id();
    
    // 변환된 데이터 반환
    return $form;
}
```

#### hookStored($wire, $form)
**호출 시점**: DB 저장 완료 후  
**용도**: 후처리 작업, 로그 기록, 알림 발송

```php
public function hookStored($wire, $form)
{
    $id = $form['id']; // 생성된 레코드 ID
    
    // 로그 기록
    Log::info("New record created", ['id' => $id]);
    
    // 알림 발송
    Notification::send(auth()->user(), new RecordCreated($form));
    
    // 연관 데이터 생성
    DB::table('related_table')->insert([
        'parent_id' => $id,
        'data' => 'additional data'
    ]);
}
```

### 3. 수정 (Update) Hooks

#### hookEditing($wire, $form)
**호출 시점**: 수정 폼이 표시되기 전  
**용도**: 데이터 전처리, 권한 체크

```php
public function hookEditing($wire, $form)
{
    // 권한 체크
    if ($form['user_id'] != auth()->id()) {
        abort(403, 'Unauthorized');
    }
    
    // 데이터 전처리
    $form['tags'] = explode(',', $form['tags']);
    
    return $form;
}
```

#### hookUpdating($wire, $form, $old)
**호출 시점**: DB 업데이트 전  
**용도**: 변경사항 검증, 데이터 변환

```php
public function hookUpdating($wire, $form, $old)
{
    // 변경 내역 로깅
    $changes = array_diff_assoc($form, $old);
    Log::info("Record updating", ['changes' => $changes]);
    
    // 데이터 검증
    if ($form['status'] == 'published' && empty($form['published_at'])) {
        $form['published_at'] = now();
    }
    
    return $form;
}
```

#### hookUpdated($wire, $form, $old)
**호출 시점**: DB 업데이트 완료 후  
**용도**: 후처리, 캐시 갱신, 알림

```php
public function hookUpdated($wire, $form, $old)
{
    // 캐시 갱신
    Cache::forget('record_' . $form['id']);
    
    // 변경 알림
    if ($old['status'] != $form['status']) {
        Notification::send(
            User::admin()->get(),
            new StatusChanged($form)
        );
    }
}
```

### 4. 삭제 (Delete) Hooks

#### hookDeleting($wire, $row)
**호출 시점**: 삭제 실행 전  
**용도**: 삭제 가능 여부 체크, 연관 데이터 처리

```php
public function hookDeleting($wire, $row)
{
    // 삭제 가능 여부 체크
    if ($row['protected']) {
        session()->flash('error', '보호된 항목은 삭제할 수 없습니다.');
        return false; // 삭제 중단
    }
    
    // 연관 데이터 체크
    $relatedCount = DB::table('related')->where('parent_id', $row['id'])->count();
    if ($relatedCount > 0) {
        session()->flash('error', '연관된 데이터가 있어 삭제할 수 없습니다.');
        return false;
    }
    
    return $row;
}
```

#### hookDeleted($wire, $row)
**호출 시점**: 삭제 완료 후  
**용도**: 후처리, 연관 데이터 정리

```php
public function hookDeleted($wire, $row)
{
    // 연관 파일 삭제
    Storage::delete($row['file_path']);
    
    // 로그 기록
    Log::info("Record deleted", ['id' => $row['id']]);
    
    // 캐시 정리
    Cache::forget('record_' . $row['id']);
}
```

### 5. 일괄 삭제 Hooks

#### hookCheckDeleting($wire, $selected)
**호출 시점**: 선택 삭제 실행 전  
**용도**: 일괄 삭제 전처리

```php
public function hookCheckDeleting($wire, $selected)
{
    // 삭제 가능한 항목만 필터링
    $deletable = [];
    foreach ($selected as $id) {
        $row = DB::table($this->tableName)->find($id);
        if (!$row->protected) {
            $deletable[] = $id;
        }
    }
    
    return $deletable;
}
```

#### hookCheckDeleted($wire, $selected)
**호출 시점**: 선택 삭제 완료 후  
**용도**: 일괄 삭제 후처리

```php
public function hookCheckDeleted($wire, $selected)
{
    // 로그 기록
    Log::info("Bulk delete completed", ['ids' => $selected]);
    
    // 캐시 정리
    foreach ($selected as $id) {
        Cache::forget('record_' . $id);
    }
}
```

## Hook 반환값 규칙

### 정상 처리
- `false` 또는 반환값 없음: 계속 진행
- 데이터 반환: 해당 데이터로 처리 진행

### 처리 중단
- View 반환: 해당 뷰를 표시하고 종료
- `false` 반환 (특정 Hook): 동작 중단

## 실제 사용 예시

### 예시 1: 사용자별 데이터 필터링

```php
class AdminPost extends Controller
{
    public function hookIndexing($wire)
    {
        // 관리자가 아닌 경우 자신의 글만 보기
        if (!auth()->user()->isAdmin()) {
            $wire->actions['where'] = [
                'user_id' => auth()->id()
            ];
        }
    }
}
```

### 예시 2: 슬러그 자동 생성

```php
class AdminProduct extends Controller
{
    public function hookStoring($wire, $form)
    {
        // 슬러그 자동 생성
        if (empty($form['slug'])) {
            $form['slug'] = Str::slug($form['name']);
        }
        
        // SKU 자동 생성
        if (empty($form['sku'])) {
            $form['sku'] = 'PRD-' . strtoupper(Str::random(8));
        }
        
        return $form;
    }
}
```

### 예시 3: 소프트 삭제와 하드 삭제 구분

```php
class AdminUser extends Controller
{
    public function hookDeleting($wire, $row)
    {
        // 관리자는 하드 삭제 불가
        if ($row['role'] == 'admin') {
            // 소프트 삭제로 변경
            DB::table('users')
                ->where('id', $row['id'])
                ->update(['deleted_at' => now()]);
            
            return false; // 하드 삭제 중단
        }
        
        return $row;
    }
}
```

### 예시 4: 상태 변경 시 알림

```php
class AdminOrder extends Controller
{
    public function hookUpdated($wire, $form, $old)
    {
        // 주문 상태 변경 시 이메일 발송
        if ($old['status'] != $form['status']) {
            $user = User::find($form['user_id']);
            
            Mail::to($user->email)->send(
                new OrderStatusChanged($form, $old['status'])
            );
        }
    }
}
```

## Hook 디버깅

Hook 실행 여부를 확인하려면:

```php
public function hookIndexing($wire)
{
    // 로그 기록
    Log::debug('hookIndexing called', [
        'controller' => get_class($this),
        'user' => auth()->id()
    ]);
    
    // 또는 dd() 사용
    // dd('Hook is working!');
}
```

## 주의사항

1. **반환값 필수**: `hookIndexed`, `hookStoring`, `hookUpdating` 등은 반드시 데이터를 반환해야 합니다.
2. **트랜잭션**: DB 작업 Hook은 트랜잭션 내에서 실행됩니다.
3. **에러 처리**: Hook 내 예외는 전체 작업을 중단시킵니다.
4. **성능**: Hook은 매 요청마다 실행되므로 무거운 작업은 피하세요.

## Best Practices

1. **단일 책임**: 각 Hook은 하나의 책임만 가지도록 작성
2. **에러 메시지**: 사용자에게 명확한 에러 메시지 제공
3. **로깅**: 중요한 작업은 로그 기록
4. **검증**: 데이터 검증은 `hookStoring`과 `hookUpdating`에서 수행
5. **정리 작업**: 삭제 시 연관 데이터 정리는 `hookDeleted`에서 수행