<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates;

use Livewire\Component;

class AdminTemplateSearch extends Component
{
    public $searchType = 'title'; // 검색 유형
    public $searchQuery = ''; // 검색어
    public $statusFilter = ''; // 상태 필터
    public $dateFrom = ''; // 시작 날짜
    public $dateTo = ''; // 종료 날짜
    public $minId = ''; // 최소 ID
    public $maxId = ''; // 최대 ID
    
    public $showAdvanced = false; // 고급 검색 표시 여부
    
    protected $queryString = [
        'searchType' => ['except' => 'title', 'as' => 'filter_type'],
        'searchQuery' => ['except' => '', 'as' => 'filter_query'],
        'statusFilter' => ['except' => '', 'as' => 'filter_status'],
        'dateFrom' => ['except' => '', 'as' => 'filter_date_from'],
        'dateTo' => ['except' => '', 'as' => 'filter_date_to'],
        'minId' => ['except' => '', 'as' => 'filter_min_id'],
        'maxId' => ['except' => '', 'as' => 'filter_max_id'],
    ];
    
    public function mount()
    {
        // URL 쿼리 스트링에서 필터 값 초기화
        $this->searchType = request()->get('filter_type', 'title');
        $this->searchQuery = request()->get('filter_query', '');
        $this->statusFilter = request()->get('filter_status', '');
        $this->dateFrom = request()->get('filter_date_from', '');
        $this->dateTo = request()->get('filter_date_to', '');
        $this->minId = request()->get('filter_min_id', '');
        $this->maxId = request()->get('filter_max_id', '');
    }
    
    public function updatedSearchQuery()
    {
        $this->applyFilters();
    }
    
    public function updatedSearchType()
    {
        $this->applyFilters();
    }
    
    public function updatedStatusFilter()
    {
        $this->applyFilters();
    }
    
    public function updatedDateFrom()
    {
        $this->applyFilters();
    }
    
    public function updatedDateTo()
    {
        $this->applyFilters();
    }
    
    public function updatedMinId()
    {
        $this->applyFilters();
    }
    
    public function updatedMaxId()
    {
        $this->applyFilters();
    }
    
    public function applyFilters()
    {
        // 필터가 변경될 때마다 이벤트 발생
        $this->dispatch('filtersUpdated', [
            'searchType' => $this->searchType,
            'searchQuery' => $this->searchQuery,
            'statusFilter' => $this->statusFilter,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'minId' => $this->minId,
            'maxId' => $this->maxId,
        ]);
    }
    
    public function toggleAdvanced()
    {
        $this->showAdvanced = !$this->showAdvanced;
    }
    
    public function setQuickDate($days)
    {
        if ($days === 7) {
            $this->dateFrom = now()->subDays(7)->format('Y-m-d');
        } elseif ($days === 30) {
            $this->dateFrom = now()->subMonth()->format('Y-m-d');
        } elseif ($days === 90) {
            $this->dateFrom = now()->subMonths(3)->format('Y-m-d');
        }
        $this->dateTo = now()->format('Y-m-d');
        $this->applyFilters();
    }
    
    public function clearFilters()
    {
        $this->searchType = 'title';
        $this->searchQuery = '';
        $this->statusFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->minId = '';
        $this->maxId = '';
        $this->showAdvanced = false;
        
        $this->applyFilters();
    }
    
    public function search()
    {
        $this->applyFilters();
    }
    
    public function render()
    {
        return view('jiny-admin2::__admin.admin-templates.admin-template-search');
    }
}