<?php

namespace Jiny\Admin2\App\Http\Livewire;

use Livewire\Component;

class AdminSearch extends Component
{
    public $jsonData;
    public $filters = [];
    
    public function mount($jsonData = null)
    {
        $this->jsonData = $jsonData;
        
        // 검색 가능한 필드들을 필터로 초기화
        if (isset($jsonData['searchable'])) {
            foreach ($jsonData['searchable'] as $field) {
                $this->filters['filter_' . $field] = '';
            }
        }
    }
    
    public function search()
    {
        // 검색 이벤트 발생 - 필터 조건 전달
        $this->dispatch('search-filters', filters: $this->filters);
    }
    
    public function resetFilters()
    {
        // 필터 값만 초기화 (구조는 유지)
        foreach ($this->filters as $key => $value) {
            $this->filters[$key] = '';
        }
        
        // 초기화 이벤트 발생
        $this->dispatch('search-reset');
    }
    
    public function render()
    {
        return view('jiny-admin2::livewire.admin-search');
    }
}