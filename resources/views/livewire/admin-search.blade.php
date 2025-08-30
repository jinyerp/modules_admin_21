<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
    <form wire:submit="search" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $searchableFields = $jsonData['index']['searchable'] ?? $jsonData['searchable'] ?? [];
            @endphp
            @if(!empty($searchableFields))
                @foreach($searchableFields as $field)
                    <div>
                        <label for="filter_{{ $field }}" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ ucfirst(str_replace('_', ' ', $field)) }}
                        </label>
                        <input type="text" 
                               id="filter_{{ $field }}"
                               wire:model="filters.filter_{{ $field }}" 
                               placeholder="{{ ucfirst($field) }} 검색..."
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                @endforeach
            @endif
        </div>
        
        <div class="flex justify-end space-x-2">
            <button type="button" 
                    wire:click="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                초기화
            </button>
            <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                검색
            </button>
        </div>
    </form>
</div>