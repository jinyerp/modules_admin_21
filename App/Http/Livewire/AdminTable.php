<?php

namespace Jiny\Admin\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Jiny\Admin\App\Models\AdminUserSession;

class AdminTable extends Component
{
    use WithPagination;

    // JSON 데이터 및 컨트롤러
    public $jsonData;
    protected $controller = null;
    protected $controllerClass = null;

    // 페이지네이션 설정
    public $perPage = 10;
    
    // 페이지 로딩 시간
    public $loadTime = 0;
    protected $startTime;

    // 정렬 설정
    #[Url]
    public $sortField = 'created_at';
    #[Url]
    public $sortDirection = 'desc';

    // 검색 필터들
    public $filters = [];
    public $search = '';
    public $filter = [];

    // 체크박스 선택 관련
    public $selectedAll = false;
    public $selected = [];
    public $selectedCount = 0;
    
    // 이벤트 리스너
    protected $listeners = [
        'search-updated' => 'updateSearch',
        'filter-updated' => 'updateFilter',
        'sort-updated' => 'updateSort',
        'perPage-updated' => 'updatePerPage',
        'search-reset' => 'resetSearch'
    ];


    /**
     * Hook 메서드 호출
     */
    public function hook($method, ...$args) 
    { 
        return $this->call($method, ...$args); 
    }
    
    public function call($method, ...$args)
    {
        // 컨트롤러가 있고 메서드가 존재하면 호출
        if($this->controller && method_exists($this->controller, $method)) {
            return $this->controller->$method($this, ...$args);
        }
        
        // 기본 메서드 체크
        if(method_exists($this, $method)) {
            return $this->$method(...$args);
        }
        
        return null;
    }
    
    // 세션 종료 메서드 (기본 구현)
    public function terminateSession($id)
    {
        // 먼저 컨트롤러의 Hook 메서드 확인
        if($this->controller && method_exists($this->controller, 'hookTerminateSession')) {
            return $this->controller->hookTerminateSession($this, $id);
        }
        
        // 기본 처리
        try {
            $session = AdminUserSession::find($id);
            if ($session && $session->is_active) {
                $session->is_active = false;
                $session->save();
                session()->flash('success', '세션이 종료되었습니다.');
            }
        } catch (\Exception $e) {
            session()->flash('error', '세션 종료 중 오류가 발생했습니다.');
        }
        $this->resetPage();
    }
    
    // 세션 재발급 메서드
    public function regenerateSession($id)
    {
        // 컨트롤러의 Hook 메서드 호출
        if($this->controller && method_exists($this->controller, 'hookRegenerateSession')) {
            return $this->controller->hookRegenerateSession($this, $id);
        }
        
        session()->flash('info', '세션 재발급 기능이 구현되지 않았습니다.');
        $this->resetPage();
    }

    public function mount($jsonData = null)
    {
        $this->startTime = microtime(true);
        
        if ($jsonData) {
            $this->jsonData = $jsonData;

            // 페이지네이션 설정
            if (isset($jsonData['index']['pagination']['perPage'])) {
                $this->perPage = $jsonData['index']['pagination']['perPage'];
            }

            // 정렬 설정
            if (isset($jsonData['index']['sorting'])) {
                $this->sortField = $jsonData['index']['sorting']['default'] ?? 'created_at';
                $this->sortDirection = $jsonData['index']['sorting']['direction'] ?? 'desc';
            }
            
            // 동적 쿼리 조건이 있으면 필터에 초기값 설정
            if (isset($jsonData['queryConditions']) && is_array($jsonData['queryConditions'])) {
                foreach ($jsonData['queryConditions'] as $field => $value) {
                    // filters 배열에 추가 (UI 반영용)
                    $this->filters[$field] = $value;
                }
            }
        }
        
        // 컨트롤러 설정
        $this->setupController();
    }
    
    /**
     * 컨트롤러 설정
     */
    protected function setupController()
    {
        // URL에서 현재 경로 확인
        $currentUrl = request()->url();
        
        // 컨트롤러 클래스 결정
        if (strpos($currentUrl, '/admin/user/sessions') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminSessions\AdminSessions::class;
        } elseif (strpos($currentUrl, '/admin/user/logs') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminUserLogs\AdminUserLogs::class;
        } elseif (strpos($currentUrl, '/admin/users') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminUsers\AdminUsers::class;
        } elseif (strpos($currentUrl, '/admin/user/type') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminUsertype\AdminUsertype::class;
        } elseif (strpos($currentUrl, '/admin/hello') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHello::class;
        } elseif (strpos($currentUrl, '/admin/templates') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplates::class;
        } elseif (strpos($currentUrl, '/admin/test') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTest::class;
        }
        
        // 컨트롤러 인스턴스 생성
        if ($this->controllerClass && class_exists($this->controllerClass)) {
            $this->controller = new $this->controllerClass();
        }
    }
    
    #[On('search-updated')]
    public function updateSearch($search)
    {
        $this->search = $search;
        $this->resetPage();
    }
    
    #[On('filter-updated')]
    public function updateFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }
    
    #[On('sort-updated')]
    public function updateSort($sortBy)
    {
        $this->sortField = $sortBy;
        $this->sortDirection = 'asc';
        $this->resetPage();
    }
    
    #[On('perPage-updated')]
    public function updatePerPage($perPage)
    {
        $this->perPage = $perPage;
        $this->resetPage();
    }
    
    #[On('search-reset')]
    public function resetSearch()
    {
        $this->search = '';
        $this->filter = [];
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[On('search-filters')]
    public function handleSearchFilters($filters)
    {
        $this->filters = $filters;
        $this->resetPage();
    }

    #[On('search-reset')]
    public function handleSearchReset()
    {
        $this->filters = [];
        $this->resetPage();
    }

    // 전체 선택 체크박스 처리
    public function updatedSelectedAll($value)
    {
        if ($value) {
            // 현재 페이지의 모든 ID 선택
            $this->selected = [];
            foreach ($this->rows as $row) {
                $this->selected[] = (string) $row->id;
            }
        } else {
            // 모든 선택 해제
            $this->selected = [];
        }

        $this->selectedCount = count($this->selected);
    }

    // 개별 체크박스 처리
    public function updatedSelected()
    {
        $currentPageIds = $this->rows->pluck('id')->map(function($id) {
            return (string) $id;
        })->toArray();

        // 현재 페이지의 모든 항목이 선택되었는지 확인
        $currentPageSelectedCount = count(array_intersect($this->selected, $currentPageIds));

        if ($currentPageSelectedCount == count($currentPageIds) && count($currentPageIds) > 0) {
            $this->selectedAll = true;
        } else {
            $this->selectedAll = false;
        }

        $this->selectedCount = count($this->selected);
    }

    // 페이지 변경 시 선택 초기화
    public function updatingPage()
    {
        $this->selectedAll = false;
        $this->selected = [];
        $this->selectedCount = 0;
    }

    // perPage 변경 시 선택 초기화
    public function updatedPerPage()
    {
        $this->selectedAll = false;
        $this->selected = [];
        $this->selectedCount = 0;
        $this->resetPage();
    }

    // 선택된 항목 삭제 요청
    public function requestDeleteSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        // AdminDelete 컴포넌트에 이벤트 전달
        $this->dispatch('delete-multiple', ids: $this->selected);
    }

    // 개별 항목 삭제 요청
    public function requestDeleteSingle($id)
    {
        // AdminDelete 컴포넌트에 이벤트 전달
        $this->dispatch('delete-single', id: $id);
    }

    // 삭제 완료 이벤트 처리
    #[On('delete-completed')]
    public function handleDeleteCompleted($message = null)
    {
        // 선택 초기화
        $this->selectedAll = false;
        $this->selected = [];
        $this->selectedCount = 0;

        // 성공 메시지 표시
        if ($message) {
            session()->flash('success', $message);
        }

        // 페이지 새로고침
        $this->resetPage();
    }

    /**
     * 세션 테이블 전용 데이터 조회
     */
    protected function getSessionRows()
    {
        $query = AdminUserSession::with('user');

        // 검색어 적용
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->whereHas('user', function($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhere('ip_address', 'like', '%' . $this->search . '%');
            });
        }

        // 필터 적용
        if (!empty($this->filter)) {
            foreach ($this->filter as $key => $value) {
                if ($value !== '' && $value !== null) {
                    $query->where($key, $value);
                }
            }
        }

        // 정렬 및 페이지네이션
        return $query->orderBy($this->sortField, $this->sortDirection)
                     ->paginate($this->perPage);
    }

    public function getRowsProperty()
    {
        // 테이블 이름 가져오기
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';

        // 특별 처리가 필요한 테이블 (Eloquent 모델 사용)
        if ($tableName === 'admin_user_sessions') {
            return $this->getSessionRows();
        }

        // 쿼리 생성
        $query = DB::table($tableName);

        // 동적 쿼리 조건 적용 (컨트롤러에서 전달된 queryConditions)
        if (isset($this->jsonData['queryConditions']) && is_array($this->jsonData['queryConditions'])) {
            foreach ($this->jsonData['queryConditions'] as $field => $value) {
                if ($value !== '' && $value !== null) {
                    // 특별한 조건 처리
                    if ($field === 'date_from') {
                        $query->where('created_at', '>=', $value);
                    } elseif ($field === 'date_to') {
                        $query->where('created_at', '<=', $value);
                    } else {
                        // 일반 조건
                        $query->where($field, $value);
                    }
                }
            }
        }

        // 검색어 적용
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        // 필터 조건 적용
        if (!empty($this->filter)) {
            foreach ($this->filter as $key => $value) {
                if ($value !== '' && $value !== null) {
                    $query->where($key, $value);
                }
            }
        }
        
        // 기존 필터 조건 적용 (filter_컬럼명 형식) - 하위 호환성
        if (!empty($this->filters)) {
            foreach ($this->filters as $filterKey => $filterValue) {
                if (!empty($filterValue)) {
                    // filter_ 접두사 제거하여 실제 컬럼명 추출
                    $column = str_replace('filter_', '', $filterKey);
                    $query->where($column, 'like', '%' . $filterValue . '%');
                }
            }
        }

        // 정렬 및 페이지네이션
        return $query->orderBy($this->sortField, $this->sortDirection)
                     ->paginate($this->perPage);
    }

    public function render()
    {
        $rows = $this->rows;
        
        // hookIndexed 호출 (데이터 조회 후 처리)
        if ($this->controller && method_exists($this->controller, 'hookIndexed')) {
            $rows = $this->controller->hookIndexed($this, $rows);
        }
        
        // 페이지 로딩 시간 계산
        $this->loadTime = microtime(true) - $this->startTime;

        $tablePath = $this->jsonData['index']['tableLayoutPath'] ?? 'jiny-admin::template.livewire.admin-table';

        return view($tablePath, [
            'rows' => $rows,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'selectedCount' => $this->selectedCount,
            'jsonData' => $this->jsonData,
            'perPage' => $this->perPage,
            'selected' => $this->selected,
            'selectedAll' => $this->selectedAll,
            'loadTime' => $this->loadTime
        ]);
    }
}
