<?php

namespace Jiny\Admin2\App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AdminShow extends Component
{
    // 레코드 ID
    public $itemId;
    
    // 데이터
    public $item;
    public $data = [];
    
    // 설정
    public $jsonData;
    public $controller;
    public $controllerClass;
    
    // 표시 설정
    public $sections = [];
    public $display = [];
    
    public function mount($controller = null, $id = null)
    {
        $this->itemId = $id;
        
        // 컨트롤러 클래스 설정
        if ($controller) {
            $this->controllerClass = $controller;
            $this->controller = new $controller();
            
            // JSON 데이터 로드
            $jsonPath = dirname((new \ReflectionClass($this->controller))->getFileName());
            $className = class_basename($this->controllerClass);
            $jsonFile = $jsonPath . '/' . $className . '.json';
            
            if (file_exists($jsonFile)) {
                $this->jsonData = json_decode(file_get_contents($jsonFile), true);
                $this->initializeFromJson();
            }
        }
        
        // 데이터 로드
        $this->loadData();
    }
    
    protected function initializeFromJson()
    {
        // 섹션 설정
        $this->sections = $this->jsonData['show']['sections'] ?? [];
        
        // 표시 설정
        $this->display = $this->jsonData['show']['display'] ?? [];
    }
    
    protected function loadData()
    {
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        $this->item = DB::table($tableName)->find($this->itemId);
        
        if (!$this->item) {
            session()->flash('error', '항목을 찾을 수 없습니다.');
            $redirectPath = $this->jsonData['redirect'] ?? '/admin2/templates';
            return redirect($redirectPath);
        }
        
        // 객체를 배열로 변환
        $this->data = (array) $this->item;
        
        // 표시 전 훅 실행
        if ($this->controller && method_exists($this->controller, 'hookShowing')) {
            $this->data = $this->controller->hookShowing($this, $this->data);
        }
        
        // 데이터 포맷팅
        $this->formatData();
    }
    
    protected function formatData()
    {
        // 날짜 형식 지정
        $dateFormat = $this->display['dateFormat'] ?? 'Y-m-d';
        $datetimeFormat = $this->display['datetimeFormat'] ?? 'Y-m-d H:i:s';
        
        foreach ($this->data as $field => $value) {
            // 날짜 필드 처리
            if (in_array($field, ['created_at', 'updated_at', 'deleted_at'])) {
                if ($value) {
                    $this->data[$field . '_formatted'] = date($datetimeFormat, strtotime($value));
                }
            }
            
            // Boolean 필드 처리
            if (is_bool($value) || in_array($field, ['enable', 'is_default', 'active', 'published'])) {
                $booleanLabels = $this->display['booleanLabels'] ?? [
                    'true' => 'Yes',
                    'false' => 'No'
                ];
                $this->data[$field . '_label'] = $value ? $booleanLabels['true'] : $booleanLabels['false'];
            }
            
            // NULL 값 처리
            if ($value === null) {
                $this->data[$field . '_display'] = $this->display['nullLabel'] ?? '-';
            }
        }
    }
    
    public function edit()
    {
        $routeName = $this->jsonData['routes']['prefix'] . '.edit' ?? 'admin2.templates.edit';
        return redirect()->route($routeName, $this->itemId);
    }
    
    public function delete()
    {
        $routeName = $this->jsonData['routes']['prefix'] . '.delete' ?? 'admin2.templates.delete';
        return redirect()->route($routeName, $this->itemId);
    }
    
    public function backToList()
    {
        $redirectPath = $this->jsonData['redirect'] ?? '/admin2/templates';
        return redirect($redirectPath);
    }
    
    public function createNew()
    {
        $routeName = $this->jsonData['routes']['prefix'] . '.create' ?? 'admin2.templates.create';
        return redirect()->route($routeName);
    }
    
    public function render()
    {
        $viewPath = $this->jsonData['show']['viewPath'] ?? 'jiny-admin2::livewire.admin-show';
        
        return view($viewPath, [
            'item' => $this->item,
            'data' => $this->data,
            'title' => $this->jsonData['show']['heading']['title'] ?? '상세 정보',
            'description' => $this->jsonData['show']['heading']['description'] ?? '',
            'sections' => $this->sections,
            'features' => $this->jsonData['show']['features'] ?? [],
            'editRoute' => $this->jsonData['routes']['prefix'] . '.edit' ?? null,
            'deleteRoute' => $this->jsonData['routes']['prefix'] . '.delete' ?? null,
            'listRoute' => $this->jsonData['redirect'] ?? '/admin2/templates',
            'createRoute' => $this->jsonData['routes']['prefix'] . '.create' ?? null
        ]);
    }
}