{{-- 
    검색 필드 템플릿
    AdminTest 모듈의 검색 폼 필드 정의
--}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    {{-- 제목 검색 필드 --}}
    <div>
        <label for="search_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            제목
        </label>
        <input type="text" 
               id="search_title"
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
               placeholder="제목으로 검색..." 
               wire:model.live.debounce.300ms="filters.filter_title">
    </div>

    {{-- 설명 검색 필드 --}}
    <div>
        <label for="search_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            설명
        </label>
        <input type="text" 
               id="search_description"
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
               placeholder="설명으로 검색..." 
               wire:model.live.debounce.300ms="filters.filter_description">
    </div>

    {{-- 상태 필터 --}}
    <div>
        <label for="filter_enable" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            상태
        </label>
        <select id="filter_enable"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                wire:model.live="filters.filter_enable">
            <option value="">전체</option>
            <option value="1">활성화</option>
            <option value="0">비활성화</option>
        </select>
    </div>
</div>