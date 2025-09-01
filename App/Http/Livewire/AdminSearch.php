<?php

namespace Jiny\Admin\App\Http\Livewire;

use Livewire\Component;

class AdminSearch extends Component
{
    public $jsonData;
    public $search = '';
    public $filter = [];
    public $sortBy = 'created_at';
    public $perPage = 10;
    public $filters = [];

    public function mount($jsonData = null)
    {
        $this->jsonData = $jsonData;

        // 검색 가능한 필드들을 필터로 초기화
        // index 안에 있는 searchable 확인, 없으면 최상위 searchable 확인
        $searchableFields = null;

        if (isset($jsonData['index']['searchable'])) {
            $searchableFields = $jsonData['index']['searchable'];
        } elseif (isset($jsonData['searchable'])) {
            // 이전 버전 호환성
            $searchableFields = $jsonData['searchable'];
        }

        if ($searchableFields) {
            foreach ($searchableFields as $field) {
                $this->filters['filter_' . $field] = '';
            }
        }
    }
    
    public function updatedSearch($value)
    {
        // 검색어 변경 시 테이블로 이벤트 전달
        $this->dispatch('search-updated', search: $value);
    }
    
    public function updatedFilter($value, $key)
    {
        // 필터 변경 시 테이블로 이벤트 전달
        $this->dispatch('filter-updated', filter: $this->filter);
    }
    
    public function updatedSortBy($value)
    {
        // 정렬 변경 시 테이블로 이벤트 전달
        $this->dispatch('sort-updated', sortBy: $value);
    }
    
    public function updatedPerPage($value)
    {
        // 페이지당 개수 변경 시 테이블로 이벤트 전달
        $this->dispatch('perPage-updated', perPage: $value);
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
    
    public function resetSearch()
    {
        $this->search = '';
        $this->filter = [];
        $this->sortBy = 'created_at';
        
        // 초기화 이벤트 발생
        $this->dispatch('search-reset');
        $this->dispatch('search-updated', search: '');
        $this->dispatch('filter-updated', filter: []);
    }

    public function render()
    {
        $viewPath = $this->jsonData['index']['searchLayoutPath'] ?? 'jiny-admin::template.livewire.admin-search';
        return view($viewPath);
    }
}
