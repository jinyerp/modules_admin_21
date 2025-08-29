<?php

namespace Jiny\Admin2\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AdminTable extends Component
{
    use WithPagination;

    public $jsonData = [];
    protected $controller;
    protected $model;
    protected $actions = [];

    #[Url]
    public $search = '';

    public $perPage = 10;

    #[Url]
    public $sortField = 'created_at';

    #[Url]
    public $sortDirection = 'desc';

    public function mount()
    {
        // 컨트롤러 인스턴스 생성
        if (isset($this->jsonData['controller']) && class_exists($this->jsonData['controller'])) {
            $this->controller = new $this->jsonData['controller'];
        }

        // 모델 클래스 설정 (table.model 경로에서 가져오기)
        if (isset($this->jsonData['table']['model']) && class_exists($this->jsonData['table']['model'])) {
            $this->model = $this->jsonData['table']['model'];
        } elseif (isset($this->jsonData['model']) && class_exists($this->jsonData['model'])) {
            // 이전 버전 호환성을 위한 fallback
            $this->model = $this->jsonData['model'];
        } else {
            // 모델이 설정되지 않은 경우 null로 설정 (render에서 오류 처리)
            $this->model = null;
        }

        // 페이징 설정
        if (isset($this->jsonData['paging'])) {
            $this->perPage = $this->jsonData['paging'];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    protected function getListeners()
    {
        return ['refreshComponent' => '$refresh'];
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
        if (!$this->model) {
            return;
        }

        $modelClass = $this->model;
        $item = $modelClass::find($itemId);
        if ($item) {
            $item->enable = !$item->enable;
            $item->save();
        }
    }

    public function deleteItem($itemId)
    {
        if (!$this->model) {
            return;
        }

        $modelClass = $this->model;
        $item = $modelClass::find($itemId);
        if ($item) {
            $item->delete();
        }
    }

    public function render()
    {
        // Hook: 데이터 조회 전 실행
        if ($this->controller && method_exists($this->controller, 'hookIndexing')) {
            $result = $this->controller->hookIndexing($this);
            if ($result !== false && $result !== null) {
                // Hook이 뷰를 반환한 경우 해당 뷰를 표시
                return $result;
            }
        }

        // 모델이 설정되지 않은 경우 에러 처리
        if (!$this->model) {
            return view('jiny-admin2::errors.message', [
                'message' => '모델 클래스가 JSON 설정 파일에 정의되지 않았습니다. table.model 또는 model 필드를 설정해주세요.'
            ]);
        }

        // 모델 클래스 존재 여부 확인
        if (!class_exists($this->model)) {
            return view('jiny-admin2::errors.message', [
                'message' => "모델 클래스 '{$this->model}'를 찾을 수 없습니다. 클래스 경로를 확인해주세요."
            ]);
        }

        $modelClass = $this->model;
        $query = $modelClass::query();

        // actions['where'] 조건이 설정된 경우 적용
        if (isset($this->actions['where'])) {
            foreach ($this->actions['where'] as $field => $condition) {
                if (is_array($condition)) {
                    // 연산자가 지정된 경우
                    foreach ($condition as $operator => $value) {
                        $query->where($field, $operator, $value);
                    }
                } else {
                    // 단순 값인 경우 (기본 = 연산자)
                    $query->where($field, $condition);
                }
            }
        }

        // 검색 조건 적용 - 동적 검색 필드 설정
        $searchableFields = $this->getSearchableFields();
        $query->when($this->search, function ($q) use ($searchableFields) {
            $q->where(function ($subQuery) use ($searchableFields) {
                foreach ($searchableFields as $field) {
                    $subQuery->orWhere($field, 'like', '%' . $this->search . '%');
                }
            });
        });

        // 정렬 및 페이지네이션
        $rows = $query->orderBy($this->sortField, $this->sortDirection)
                      ->paginate($this->perPage);

        // Hook: 데이터 조회 후 실행
        if ($this->controller && method_exists($this->controller, 'hookIndexed')) {
            $rows = $this->controller->hookIndexed($this, $rows);
        }

        // 뷰 경로 확인 및 렌더링
        $viewPath = $this->jsonData['index']['tablePath'];

        return view($viewPath, [
            'rows' => $rows
        ]);
    }

    /**
     * 검색 가능한 필드 목록 반환
     * JSON 설정에서 가져오거나 기본값 사용
     */
    protected function getSearchableFields()
    {
        if (isset($this->jsonData['searchable'])) {
            return $this->jsonData['searchable'];
        }

        // 기본 검색 필드
        return ['name', 'email'];
    }
}
