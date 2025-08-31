<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    {{-- Name 검색 필드 --}}
    <div>
        <label for="search_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            이름
        </label>
        <input type="text" 
               id="search_name"
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
               placeholder="이름으로 검색..." 
               wire:model.live.debounce.300ms="filters.filter_name">
    </div>

    {{-- Message 검색 필드 --}}
    <div>
        <label for="search_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            메시지
        </label>
        <input type="text" 
               id="search_message"
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
               placeholder="메시지로 검색..." 
               wire:model.live.debounce.300ms="filters.filter_message">
    </div>

    {{-- 상태 필터 --}}
    <div>
        <label for="filter_is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            상태
        </label>
        <select id="filter_is_active"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                wire:model.live="filters.filter_is_active">
            <option value="">전체</option>
            <option value="1">활성</option>
            <option value="0">비활성</option>
        </select>
    </div>
</div>