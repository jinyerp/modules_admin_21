<div class="mt-6">
    <!-- 성공/오류 메시지 -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- 선택된 항목 정보 및 삭제 버튼 -->
    @if($selectedCount > 0)
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg flex justify-between items-center">
            <span class="text-sm text-blue-700">
                {{ $selectedCount }}개 항목이 선택되었습니다.
            </span>
            <button wire:click="requestDeleteSelected"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                선택 삭제
            </button>
        </div>
    @endif

    <!-- 테이블 -->
    @if(isset($jsonData['index']['tablePath']) && !empty($jsonData['index']['tablePath']))

        @includeIf($jsonData['index']['tablePath'])

    @else
        @include('jiny-admin::template.components.config-error', [
            'title' => '테이블 설정 오류',
            'config' => 'index.tablePath'
        ])
    @endif

    <!-- 페이지네이션 및 결과 정보 -->
    <div class="mt-6 bg-white px-4 py-3 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- 결과 정보 표시 -->
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-700">
                    총 <span class="font-semibold text-gray-900">{{ $rows->total() }}</span>개 중 
                    <span class="font-semibold text-gray-900">{{ $rows->firstItem() ?? 0 }}</span>-<span class="font-semibold text-gray-900">{{ $rows->lastItem() ?? 0 }}</span>번째 표시
                </div>
                
                <div class="flex items-center gap-2">
                    <label for="perPage" class="text-sm text-gray-600">페이지당:</label>
                    <select id="perPage" 
                            wire:model.live="perPage"
                            class="text-sm border border-gray-300 bg-white px-3 py-1.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            
            <!-- 페이지네이션 -->
            @if($rows->hasPages())
                <div class="flex items-center gap-1">
                    {{-- 처음 페이지 --}}
                    @if (!$rows->onFirstPage())
                        <button wire:click="gotoPage(1)" 
                                class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif

                    {{-- 이전 페이지 --}}
                    @if ($rows->previousPageUrl())
                        <button wire:click="previousPage" 
                                class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @else
                        <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-not-allowed">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif

                    {{-- 페이지 번호들 --}}
                    @php
                        $start = max(1, $rows->currentPage() - 2);
                        $end = min($rows->lastPage(), $rows->currentPage() + 2);
                    @endphp

                    @if($start > 1)
                        <button wire:click="gotoPage(1)" 
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            1
                        </button>
                        @if($start > 2)
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300">
                                ...
                            </span>
                        @endif
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $rows->currentPage())
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-indigo-600 z-10">
                                {{ $i }}
                            </span>
                        @else
                            <button wire:click="gotoPage({{ $i }})" 
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                {{ $i }}
                            </button>
                        @endif
                    @endfor

                    @if($end < $rows->lastPage())
                        @if($end < $rows->lastPage() - 1)
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300">
                                ...
                            </span>
                        @endif
                        <button wire:click="gotoPage({{ $rows->lastPage() }})" 
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            {{ $rows->lastPage() }}
                        </button>
                    @endif

                    {{-- 다음 페이지 --}}
                    @if ($rows->nextPageUrl())
                        <button wire:click="nextPage" 
                                class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @else
                        <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-not-allowed">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif

                    {{-- 마지막 페이지 --}}
                    @if (!$rows->onLastPage())
                        <button wire:click="gotoPage({{ $rows->lastPage() }})" 
                                class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- 페이지 로딩 시간 표시 -->
    @if(isset($loadTime))
        <div class="mt-4 text-center">
            <span class="text-xs text-gray-500">
                페이지 로딩 시간: {{ number_format($loadTime, 3) }}초
            </span>
        </div>
    @endif

</div>
