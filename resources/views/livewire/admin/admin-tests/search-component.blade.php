<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="filter_title" class="block text-sm font-medium text-gray-700">제목</label>
                    <input type="text" 
                           wire:model="filters.title" 
                           id="filter_title" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="filter_description" class="block text-sm font-medium text-gray-700">설명</label>
                    <input type="text" 
                           wire:model="filters.description" 
                           id="filter_description" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="filter_enable" class="block text-sm font-medium text-gray-700">상태</label>
                    <select wire:model="filters.enable" 
                            id="filter_enable" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">전체</option>
                        <option value="true">활성</option>
                        <option value="false">비활성</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" 
                        wire:click="resetFilters"
                        class="rounded-md bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    초기화
                </button>
                <button type="button" 
                        wire:click="search"
                        wire:loading.attr="disabled"
                        class="rounded-md bg-indigo-600 px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50">
                    <span wire:loading.remove>검색</span>
                    <span wire:loading>검색중...</span>
                </button>
            </div>
        </div>
    </div>
</div>