<div class="bg-white shadow rounded-lg mb-6">
    <div class="p-4">
        <!-- Main Search Bar -->
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search Type & Query -->
            <div class="flex-1">
                <div class="flex gap-2">
                    <!-- Search Type Dropdown -->
                    <div class="relative">
                        <select wire:model.live="searchType" 
                                class="appearance-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-8">
                            <option value="all">전체</option>
                            <option value="title">제목</option>
                            <option value="description">설명</option>
                            <option value="id">ID</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Search Input -->
                    <div class="flex-1 relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="searchQuery"
                               placeholder="검색어를 입력하세요..." 
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @if($searchQuery)
                        <button wire:click="$set('searchQuery', '')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Status Filter -->
            <div class="w-full lg:w-48">
                <select wire:model.live="statusFilter" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">활성화 상태 (전체)</option>
                    <option value="enabled">활성화</option>
                    <option value="disabled">비활성화</option>
                </select>
            </div>
            
            <!-- Search Action Buttons -->
            <div class="flex gap-2">
                <!-- Search Button -->
                <button wire:click="search"
                        class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>검색</span>
                    </div>
                </button>
                
                <!-- Clear Button -->
                <button wire:click="clearFilters"
                        class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-300 focus:outline-none">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>초기화</span>
                    </div>
                </button>
                
                <!-- Advanced Toggle Button -->
                <button wire:click="toggleAdvanced"
                        class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 focus:outline-none">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 transition-transform duration-200 {{ $showAdvanced ? 'rotate-180' : '' }}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <span>상세 검색</span>
                    </div>
                </button>
            </div>
        </div>
        
        <!-- Advanced Search Options -->
        @if($showAdvanced)
        <div class="mt-4 pt-4 border-t border-gray-200" 
             wire:transition.duration.200ms>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">등록일</label>
                    <div class="flex gap-2 items-center">
                        <input type="date" 
                               wire:model.live="dateFrom"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                        <span class="text-gray-500">~</span>
                        <input type="date" 
                               wire:model.live="dateTo"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                    </div>
                </div>
                
                <!-- ID Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID 범위</label>
                    <div class="flex gap-2 items-center">
                        <input type="number" 
                               wire:model.live="minId"
                               placeholder="최소"
                               min="1"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                        <span class="text-gray-500">~</span>
                        <input type="number" 
                               wire:model.live="maxId"
                               placeholder="최대"
                               min="1"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                    </div>
                </div>
                
                <!-- Quick Date Filters -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">빠른 선택</label>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="setQuickDate(7)"
                                class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded hover:bg-gray-200">
                            최근 7일
                        </button>
                        <button wire:click="setQuickDate(30)"
                                class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded hover:bg-gray-200">
                            최근 1개월
                        </button>
                        <button wire:click="setQuickDate(90)"
                                class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded hover:bg-gray-200">
                            최근 3개월
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Active Filters Display -->
        @if($searchQuery || $statusFilter || $dateFrom || $dateTo || $minId || $maxId)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm text-gray-600">적용된 필터:</span>
                
                @if($searchQuery)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                    {{ $searchType === 'all' ? '전체' : ($searchType === 'title' ? '제목' : ($searchType === 'description' ? '설명' : 'ID')) }}: {{ $searchQuery }}
                    <button wire:click="$set('searchQuery', '')" class="ml-1 hover:text-blue-600">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
                @endif
                
                @if($statusFilter)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
                    상태: {{ $statusFilter === 'enabled' ? '활성화' : '비활성화' }}
                    <button wire:click="$set('statusFilter', '')" class="ml-1 hover:text-green-600">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
                @endif
                
                @if($dateFrom || $dateTo)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded">
                    날짜: {{ $dateFrom ?: '시작' }} ~ {{ $dateTo ?: '끝' }}
                    <button wire:click="$set('dateFrom', ''); $set('dateTo', '')" class="ml-1 hover:text-purple-600">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
                @endif
                
                @if($minId || $maxId)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded">
                    ID: {{ $minId ?: '최소' }} ~ {{ $maxId ?: '최대' }}
                    <button wire:click="$set('minId', ''); $set('maxId', '')" class="ml-1 hover:text-orange-600">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
                @endif
                
                <button wire:click="clearFilters" 
                        class="text-xs text-gray-500 hover:text-gray-700 underline">
                    모두 지우기
                </button>
            </div>
        </div>
        @endif
    </div>
</div>