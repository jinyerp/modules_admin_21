<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;
use Jiny\Admin2\App\Http\Controllers\Admin\AdminTemplates\AdminTemplates;

class AdminTemplateList extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $category = '';
    
    #[Url]
    public $enable = '';
    
    #[Url]
    public $is_default = '';

    public $perPage = 10;

    #[Url]
    public $sortField = 'created_at';

    #[Url]
    public $sortDirection = 'desc';
    
    public $jsonData;
    public $controller;
    
    public function mount()
    {
        // 컨트롤러 인스턴스 생성 및 JSON 데이터 로드
        $this->controller = new AdminTemplates();
        
        // JSON 데이터 로드
        $jsonPath = dirname((new \ReflectionClass($this->controller))->getFileName()) . '/AdminTemplates.json';
        if (file_exists($jsonPath)) {
            $this->jsonData = json_decode(file_get_contents($jsonPath), true);
            
            // route 정보를 jsonData에 추가
            if (isset($this->jsonData['route'])) {
                $this->jsonData['currentRoute'] = $this->jsonData['route'];
            }
        }
        
        // 기본 설정 적용
        $this->perPage = $this->jsonData['index']['pagination']['perPage'] ?? 10;
        $this->sortField = $this->jsonData['index']['sorting']['default'] ?? 'created_at';
        $this->sortDirection = $this->jsonData['index']['sorting']['direction'] ?? 'desc';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }
    
    public function updatingEnable()
    {
        $this->resetPage();
    }
    
    public function updatingIsDefault()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
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

    public function toggleEnable($itemId)
    {
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        $item = DB::table($tableName)->find($itemId);
        if ($item) {
            DB::table($tableName)
                ->where('id', $itemId)
                ->update(['enable' => !$item->enable]);
        }
    }
    
    public function toggleDefault($itemId)
    {
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        
        // 기본 템플릿으로 설정
        DB::table($tableName)->update(['is_default' => false]);
        DB::table($tableName)
            ->where('id', $itemId)
            ->update(['is_default' => true]);
    }

    public function deleteItem($itemId)
    {
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        $item = DB::table($tableName)->find($itemId);
        
        if ($item) {
            // 기본 템플릿은 삭제 불가
            if ($item->is_default ?? false) {
                session()->flash('error', '기본 템플릿은 삭제할 수 없습니다.');
                return;
            }
            
            DB::table($tableName)
                ->where('id', $itemId)
                ->delete();
                
            session()->flash('success', '템플릿이 삭제되었습니다.');
        }
    }
    
    public function viewItem($itemId)
    {
        $routeName = isset($this->jsonData['route']) 
            ? $this->jsonData['route'] . '.show'
            : 'admin2.templates.show';
        return redirect()->route($routeName, $itemId);
    }
    
    public function editItem($itemId)
    {
        $routeName = isset($this->jsonData['route']) 
            ? $this->jsonData['route'] . '.edit'
            : 'admin2.templates.edit';
        return redirect()->route($routeName, $itemId);
    }

    public function render()
    {
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        $query = DB::table($tableName);
        
        // 기본 where 조건 적용
        if (isset($this->jsonData['table']['where']['default'])) {
            foreach ($this->jsonData['table']['where']['default'] as $condition) {
                if (count($condition) === 3) {
                    $query->where($condition[0], $condition[1], $condition[2]);
                } elseif (count($condition) === 2) {
                    $query->where($condition[0], $condition[1]);
                }
            }
        }
        
        // 컨트롤러 훅 실행 (조회 전)
        if (method_exists($this->controller, 'hookIndexing')) {
            $result = $this->controller->hookIndexing($this);
            if ($result !== false) {
                return $result;
            }
        }

        // 검색 조건 적용
        $searchableFields = $this->jsonData['index']['searchable'] ?? 
                           $this->jsonData['searchable'] ?? 
                           ['name', 'slug', 'description', 'author', 'title'];
        $query->when($this->search, function ($q) use ($searchableFields) {
            $q->where(function ($subQuery) use ($searchableFields) {
                foreach ($searchableFields as $field) {
                    $subQuery->orWhere($field, 'like', '%' . $this->search . '%');
                }
            });
        });

        // 카테고리 필터
        $query->when($this->category, function ($q) {
            $q->where('category', $this->category);
        });
        
        // Enable 필터
        $query->when($this->enable !== '', function ($q) {
            $q->where('enable', $this->enable);
        });
        
        // is_default 필터
        $query->when($this->is_default !== '', function ($q) {
            $q->where('is_default', $this->is_default);
        });

        // 정렬 및 페이지네이션
        $rows = $query->orderBy($this->sortField, $this->sortDirection)
                      ->paginate($this->perPage);
        
        // 컨트롤러 훅 실행 (조회 후)
        if (method_exists($this->controller, 'hookIndexed')) {
            $rows = $this->controller->hookIndexed($this, $rows);
        }

        // 카테고리 목록 가져오기
        $categories = DB::table($tableName)
                        ->distinct()
                        ->whereNotNull('category')
                        ->pluck('category');
        
        // 테이블 컬럼 설정
        $columns = $this->jsonData['index']['table']['columns'] ?? [];
        
        // 필터 옵션
        $filters = $this->jsonData['index']['filters'] ?? [];

        return view('jiny-admin2::__admin.admin-templates.admin-template-list', [
            'rows' => $rows,
            'categories' => $categories,
            'columns' => $columns,
            'filters' => $filters,
            'features' => $this->jsonData['index']['features'] ?? [],
            'perPageOptions' => $this->jsonData['index']['pagination']['perPageOptions'] ?? [10, 25, 50, 100]
        ]);
    }
}