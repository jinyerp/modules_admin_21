<?php

namespace Jiny\Admin\App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class AdminShow extends Component
{
    // 레코드 ID
    public $itemId;

    // 데이터
    public $item;
    public $data = [];

    // 설정
    public $jsonData;
    protected $controller = null;
    public $controllerClass = null;

    // 표시 설정
    public $sections = [];
    public $display = [];

    public function mount($jsonData = null, $data = [], $id = null, $controllerClass = null)
    {
        $this->jsonData = $jsonData;
        $this->data = $data;
        $this->itemId = $id;

        // 컨트롤러 클래스 설정
        if ($controllerClass) {
            $this->controllerClass = $controllerClass;
            $this->setupController();
        } elseif (isset($this->jsonData['controllerClass'])) {
            $this->controllerClass = $this->jsonData['controllerClass'];
            $this->setupController();
        }

        // Apply display formatting if configured
        if (isset($this->jsonData['show']['display'])) {
            $this->display = $this->jsonData['show']['display'];
        }

        // Apply section configuration if available
        if (isset($this->jsonData['formSections'])) {
            $this->sections = $this->jsonData['formSections'];
        }

        // hookShowing 호출
        if ($this->controller && method_exists($this->controller, 'hookShowing')) {
            $result = $this->controller->hookShowing($this, $this->data);
            if (is_array($result)) {
                $this->data = $result;
            }
        }
    }

    /**
     * 컨트롤러 설정
     */
    protected function setupController()
    {
        // 컨트롤러 인스턴스 생성
        if ($this->controllerClass && class_exists($this->controllerClass)) {
            $this->controller = new $this->controllerClass();
            \Log::info('AdminShow: Controller loaded successfully', [
                'class' => $this->controllerClass
            ]);
        } else {
            \Log::warning('AdminShow: Controller class not found', [
                'class' => $this->controllerClass
            ]);
        }
    }

    /**
     * 삭제 요청
     */
    public function requestDelete()
    {
        if ($this->itemId) {
            $this->dispatch('delete-single', id: $this->itemId);
        }
    }

    /**
     * 삭제 완료 처리
     */
    #[On('delete-completed')]
    public function handleDeleteCompleted($message = null)
    {
        // 목록 페이지로 리다이렉트 (메시지 포함)
        $redirectUrl = '/admin2/templates';
        if (isset($this->jsonData['route']['name'])) {
            $redirectUrl = route($this->jsonData['route']['name'] . '.index');
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            $redirectUrl = route($this->jsonData['route'] . '.index');
        }

        if ($message) {
            return redirect($redirectUrl)->with('success', $message);
        }
        return redirect($redirectUrl);
    }

    public function render()
    {
        $viewPath = $this->jsonData['show']['showLayoutPath'] ?? 'jiny-admin::template.livewire.admin-show';
        return view($viewPath);
    }
}
