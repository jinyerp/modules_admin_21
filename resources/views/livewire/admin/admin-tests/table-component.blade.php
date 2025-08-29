<div>
    <div>
        <!-- 선택 삭제 버튼 -->
        @if($jsonConfig['features']['enableBulkActions'] ?? false)
    <div class="mb-4 flex justify-between items-center">
        <div class="text-sm text-gray-600 {{ count($selectedItems) > 0 ? '' : 'hidden' }}">
            <span class="font-medium">{{ count($selectedItems) }}</span>개 항목 선택됨
        </div>
        <button type="button" 
                wire:click="$dispatch('showBulkDeleteModal', { itemIds: @js($selectedItems) })"
                class="{{ count($selectedItems) > 0 ? '' : 'hidden' }} rounded-md bg-red-600 px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
            선택 삭제
        </button>
    </div>
    @endif

    <!-- 테이블 -->
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    @if($jsonConfig['features']['enableBulkActions'] ?? false)
                    <th scope="col" class="relative px-7 sm:w-12 sm:px-6">
                        <input type="checkbox" 
                               wire:model.live="selectAllChecked"
                               wire:click="selectAll"
                               class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    </th>
                    @endif
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                        <button type="button" wire:click="sortBy('id')" class="group inline-flex">
                            ID
                            @if($sortField === 'id')
                                @if($sortDirection === 'asc')
                                    <svg class="ml-2 h-5 w-5 flex-none rounded text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="ml-2 h-5 w-5 flex-none rounded text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832l-3.71 3.938a.75.75 0 01-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            @endif
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                        <button type="button" wire:click="sortBy('title')" class="group inline-flex">
                            제목
                            @if($sortField === 'title')
                                @if($sortDirection === 'asc')
                                    <svg class="ml-2 h-5 w-5 flex-none rounded text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="ml-2 h-5 w-5 flex-none rounded text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832l-3.71 3.938a.75.75 0 01-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            @endif
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">설명</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                        <button type="button" wire:click="sortBy('enable')" class="group inline-flex">
                            상태
                            @if($sortField === 'enable')
                                @if($sortDirection === 'asc')
                                    <svg class="ml-2 h-5 w-5 flex-none rounded text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="ml-2 h-5 w-5 flex-none rounded text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832l-3.71 3.938a.75.75 0 01-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            @endif
                        </button>
                    </th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">액션</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($rows as $row)
                <tr wire:key="row-{{ $row->id }}"
                    class="{{ in_array($row->id, $selectedItems) ? 'bg-yellow-50' : '' }} {{ $hierarchyEnabled && isset($row->indent_level) ? 'hierarchy-level-' . $row->indent_level : '' }}">
                    @if($jsonConfig['features']['enableBulkActions'] ?? false)
                    <td class="relative px-7 sm:w-12 sm:px-6">
                        <input type="checkbox" 
                               wire:click="toggleSelection({{ $row->id }})"
                               value="{{ $row->id }}"
                               {{ in_array($row->id, $selectedItems) ? 'checked' : '' }}
                               class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    </td>
                    @endif
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                        {{ $row->id }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                        @if($hierarchyEnabled && isset($row->indent_level))
                            <span style="padding-left: {{ $row->indent_level * 20 }}px;">
                                @if($row->hasChildren())
                                    <svg class="inline-block h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                @else
                                    <svg class="inline-block h-4 w-4 mr-1 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @endif
                            </span>
                        @endif
                        @if(isset($routes['detail']))
                            <a href="{{ route($routes['detail'], $row->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ Str::limit($row->title, 50) }}
                            </a>
                        @else
                            {{ Str::limit($row->title, 50) }}
                        @endif
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        {{ Str::limit($row->description, 80) }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        @if($row->enable)
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                활성
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">
                                비활성
                            </span>
                        @endif
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <div class="flex justify-end space-x-2">
                            @if(isset($routes['edit']))
                            <a href="{{ route($routes['edit'], $row->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900">
                                수정
                            </a>
                            @endif
                            <button type="button"
                                    wire:click.stop="confirmDelete({{ $row->id }})"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    class="text-red-600 hover:text-red-900">
                                <span wire:loading.remove wire:target="confirmDelete({{ $row->id }})">삭제</span>
                                <span wire:loading wire:target="confirmDelete({{ $row->id }})">...</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ ($jsonConfig['features']['enableBulkActions'] ?? false) ? '6' : '5' }}" 
                        class="px-3 py-8 text-center text-sm text-gray-500">
                        데이터가 없습니다.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    @if($jsonConfig['features']['enablePagination'] ?? true)
    <div class="mt-4" wire:key="pagination-{{ $rows->currentPage() }}">
        {{ $rows->links() }}
    </div>
    @endif

        <!-- 로딩 표시 -->
        <div wire:loading class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-gray-700 opacity-75 flex flex-col items-center justify-center">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
            <h2 class="text-center text-white text-xl font-semibold">로딩중...</h2>
        </div>
    </div>

    <style>
    .loader {
        border-top-color: #3490dc;
        animation: spinner 1.5s linear infinite;
    }
    
    @keyframes spinner {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .hierarchy-level-1 {
        background-color: rgb(249 250 251);
    }
    
    .hierarchy-level-2 {
        background-color: rgb(243 244 246);
    }
    
    .hierarchy-level-3 {
        background-color: rgb(229 231 235);
    }
    </style>
</div>