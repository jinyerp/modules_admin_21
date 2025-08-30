{{-- 
    검색 필드 컴포넌트
    
    이 파일은 실제 검색 입력 필드들을 포함합니다.
    template/livewire/admin-search.blade.php 레이아웃에 include되어 사용됩니다.
    
    작성 가이드:
    1. 각 검색 필드는 label과 input으로 구성
    2. wire:model.live.debounce로 실시간 검색 구현
    3. 반응형 grid 레이아웃 사용 (sm:grid-cols-2, lg:grid-cols-3)
    4. input 요소는 border가 있는 스타일 사용
    5. placeholder로 사용자에게 힌트 제공
    
    Available Variables:
    - $searchableFields: 검색 가능한 필드 목록 배열
    - $filters: 필터 옵션 설정 배열
--}}
@php
    $searchableFields = $jsonData['index']['searchable'] ?? [];
    $filters = $jsonData['index']['filters'] ?? [];
@endphp

<div class="space-y-6">
    {{-- 
        텍스트 검색 필드 섹션
        grid 레이아웃으로 반응형 배치
    --}}
    <div>
        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-4">검색 필터</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            
            {{-- Name 검색 필드 --}}
            @if (in_array('name', $searchableFields))
                <div>
                    <label for="filter_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Name
                    </label>
                    <input 
                        type="text" 
                        id="filter_name" 
                        wire:model.live.debounce.300ms="filters.filter_name"
                        placeholder="템플릿 이름 검색"
                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm placeholder-gray-400 
                               focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500
                               dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-500"
                    />
                </div>
            @endif

            {{-- Slug 검색 필드 --}}
            @if (in_array('slug', $searchableFields))
                <div>
                    <label for="filter_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Slug
                    </label>
                    <input 
                        type="text" 
                        id="filter_slug" 
                        wire:model.live.debounce.300ms="filters.filter_slug"
                        placeholder="Slug 검색"
                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm placeholder-gray-400 
                               focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500
                               dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-500"
                    />
                </div>
            @endif

            {{-- Author 검색 필드 --}}
            @if (in_array('author', $searchableFields))
                <div>
                    <label for="filter_author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Author
                    </label>
                    <input 
                        type="text" 
                        id="filter_author" 
                        wire:model.live.debounce.300ms="filters.filter_author"
                        placeholder="작성자 검색"
                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm placeholder-gray-400 
                               focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500
                               dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-500"
                    />
                </div>
            @endif

            {{-- Category 텍스트 검색 (필터에 없을 때만 표시) --}}
            @if (in_array('category', $searchableFields) && !isset($filters['category']))
                <div>
                    <label for="filter_category_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Category
                    </label>
                    <input 
                        type="text" 
                        id="filter_category_text" 
                        wire:model.live.debounce.300ms="filters.filter_category"
                        placeholder="카테고리 검색"
                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm placeholder-gray-400 
                               focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500
                               dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-500"
                    />
                </div>
            @endif

            {{-- Description 검색 필드 (전체 너비) --}}
            @if (in_array('description', $searchableFields))
                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="filter_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Description
                    </label>
                    <input 
                        type="text" 
                        id="filter_description" 
                        wire:model.live.debounce.300ms="filters.filter_description"
                        placeholder="설명에서 검색"
                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm placeholder-gray-400 
                               focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500
                               dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-500"
                    />
                </div>
            @endif
        </div>
    </div>

    {{-- 
        드롭다운 필터 섹션
        select 요소를 사용한 필터 옵션
    --}}
    @if (!empty($filters))
        <div>
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-4">필터 옵션</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                
                {{-- Status 필터 드롭다운 --}}
                @if (isset($filters['enable']))
                    <div>
                        <label for="filter_enable" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $filters['enable']['label'] ?? 'Status' }}
                        </label>
                        <select 
                            id="filter_enable" 
                            wire:model.live="filters.enable"
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm
                                   focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500
                                   dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                        >
                            @if (isset($filters['enable']['options']))
                                @foreach ($filters['enable']['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                @endif

                {{-- Category 필터 드롭다운 --}}
                @if (isset($filters['category']))
                    <div>
                        <label for="filter_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $filters['category']['label'] ?? 'Category' }}
                        </label>
                        <select 
                            id="filter_category" 
                            wire:model.live="filters.category"
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm
                                   focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500
                                   dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                        >
                            @if (isset($filters['category']['options']))
                                @foreach ($filters['category']['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                @endif

                {{-- 
                    추가 필터를 위한 자리
                    필요시 더 많은 필터 드롭다운을 추가할 수 있습니다
                --}}
            </div>
        </div>
    @endif
</div>

{{-- 
    커스텀 필드 추가 예시:
    
    날짜 범위 필터:
    <div>
        <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            날짜 범위
        </label>
        <div class="flex space-x-2">
            <input type="date" id="date_from" wire:model.live="filters.date_from" 
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm">
            <span class="self-center">~</span>
            <input type="date" id="date_to" wire:model.live="filters.date_to"
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
    
    체크박스 필터:
    <div>
        <label class="inline-flex items-center">
            <input type="checkbox" wire:model.live="filters.is_featured"
                   class="rounded border-gray-300 text-indigo-600 shadow-sm">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">추천 템플릿만 보기</span>
        </label>
    </div>
--}}