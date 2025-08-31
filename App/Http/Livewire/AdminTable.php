<?php

namespace Jiny\Admin\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class AdminTable extends Component
{
    use WithPagination;

    // JSON 데이터 및 컨트롤러
    public $jsonData;

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
        'search-reset' => 'resetSearch'
    ];


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

    public function getRowsProperty()
    {
        // 테이블 이름 가져오기
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';

        // 쿼리 생성
        $query = DB::table($tableName);

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
