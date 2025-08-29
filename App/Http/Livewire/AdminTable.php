<?php

namespace Jiny\Admin2\App\Http\Livewire;

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
    
    // 정렬 설정
    #[Url]
    public $sortField = 'created_at';
    #[Url]
    public $sortDirection = 'desc';
    
    // 검색 필터들
    public $filters = [];

    public function mount($jsonData = null)
    {
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
    
    public function getRowsProperty()
    {
        // 테이블 이름 가져오기
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        
        // 쿼리 생성
        $query = DB::table($tableName);
        
        // 필터 조건 적용 (filter_컬럼명 형식)
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
        
        $tablePath = $this->jsonData['index']['tablePath'] ?? 'jiny-admin2::livewire.admin-table';
        
        return view($tablePath, [
            'rows' => $rows,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection
        ]);
    }
}
