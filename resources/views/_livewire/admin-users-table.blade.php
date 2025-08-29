<div class="px-4 sm:px-6 lg:px-8">


    <!-- 검색 및 필터 영역 -->
    <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex-1 max-w-sm">
            <label for="search" class="sr-only">검색</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="block w-full rounded-md border-0 py-1.5 pl-10 pr-3 text-gray-900 dark:text-white dark:bg-gray-800 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm"
                       placeholder="이름 또는 이메일 검색...">
            </div>
        </div>
        <div class="flex items-center gap-2">
            <label for="perPage" class="text-sm text-gray-700 dark:text-gray-300">표시:</label>
            <select wire:model.live="perPage"
                    class="rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-gray-800 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm">
                <option value="10">10개</option>
                <option value="25">25개</option>
                <option value="50">50개</option>
                <option value="100">100개</option>
            </select>
        </div>
    </div>

    <!-- 테이블 -->
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <table class="relative min-w-full divide-y divide-gray-300 dark:divide-white/15">
                    <thead>
                        <tr>
                            <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-0 dark:text-white">
                                <button wire:click="sortBy('id')" class="group inline-flex items-center gap-x-1">
                                    ID
                                    @if($sortField === 'id')
                                        @if($sortDirection === 'asc')
                                            <svg class="h-4 w-4 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="h-4 w-4 text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                <button wire:click="sortBy('enable')" class="group inline-flex items-center gap-x-1">
                                    상태
                                    @if($sortField === 'enable')
                                        @if($sortDirection === 'asc')
                                            <svg class="h-4 w-4 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="h-4 w-4 text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                <button wire:click="sortBy('name')" class="group inline-flex items-center gap-x-1">
                                    이름
                                    @if($sortField === 'name')
                                        @if($sortDirection === 'asc')
                                            <svg class="h-4 w-4 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="h-4 w-4 text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                <button wire:click="sortBy('email')" class="group inline-flex items-center gap-x-1">
                                    이메일
                                    @if($sortField === 'email')
                                        @if($sortDirection === 'asc')
                                            <svg class="h-4 w-4 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="h-4 w-4 text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                <button wire:click="sortBy('created_at')" class="group inline-flex items-center gap-x-1">
                                    생성일
                                    @if($sortField === 'created_at')
                                        @if($sortDirection === 'asc')
                                            <svg class="h-4 w-4 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="h-4 w-4 text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="py-3.5 pr-4 pl-3 sm:pr-0">
                                <span class="sr-only">작업</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse($rows as $item)
                            <tr>
                                <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-0 dark:text-white">
                                    {{ $item->id }}
                                </td>
                                <td class="px-3 py-4 text-sm whitespace-nowrap">
                                    <button wire:click="toggleEnable({{ $item->id }})"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 dark:focus:ring-offset-gray-900 {{ $item->enable ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $item->enable ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                    </button>
                                </td>
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    {{ $item->name }}
                                </td>
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    {{ $item->email }}
                                </td>
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    {{ $item->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-0">
                                    <button wire:click="$dispatch('openModal', { component: 'admin-user-edit', arguments: { itemId: {{ $item->id }} }})"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                        편집
                                    </button>
                                    <button wire:click="deleteItem({{ $item->id }})"
                                            wire:confirm="정말 이 항목을 삭제하시겠습니까?"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        삭제
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="mt-2">등록된 사용자가 없습니다.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 페이지네이션 -->
    @if ($rows->hasPages())
        <div class="mt-4">
            <nav class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if ($rows->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark:bg-gray-800 dark:border-gray-600">
                            이전
                        </span>
                    @else
                        <button wire:click="previousPage" wire:loading.attr="disabled" type="button" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-blue-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:text-gray-200">
                            이전
                        </button>
                    @endif

                    @if ($rows->hasMorePages())
                        <button wire:click="nextPage" wire:loading.attr="disabled" type="button" class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-blue-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:text-gray-200">
                            다음
                        </button>
                    @else
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark:bg-gray-800 dark:border-gray-600">
                            다음
                        </span>
                    @endif
                </div>

                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 leading-5 dark:text-gray-400">
                            전체 <span class="font-medium">{{ $rows->total() }}</span>개 중
                            <span class="font-medium">{{ $rows->firstItem() }}</span> -
                            <span class="font-medium">{{ $rows->lastItem() }}</span> 표시
                        </p>
                    </div>

                    <div>
                        <span class="relative z-0 inline-flex shadow-sm rounded-md">
                            @if ($rows->onFirstPage())
                                <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l-md leading-5 dark:bg-gray-800 dark:border-gray-600">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @else
                                <button wire:click="previousPage" wire:loading.attr="disabled" type="button" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring ring-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:hover:text-gray-300">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif

                            @php
                                $start = $rows->currentPage() - 2;
                                $end = $rows->currentPage() + 2;
                                
                                if ($start < 1) {
                                    $start = 1;
                                    $end = 5;
                                }
                                
                                if ($end > $rows->lastPage()) {
                                    $end = $rows->lastPage();
                                    $start = $end - 4;
                                    if ($start < 1) $start = 1;
                                }
                            @endphp

                            @if ($start > 1)
                                <button wire:click="gotoPage(1)" type="button" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                    1
                                </button>
                                @if ($start > 2)
                                    <span class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                                        ...
                                    </span>
                                @endif
                            @endif

                            @foreach(range($start, $end) as $page)
                                @if($page <= $rows->lastPage())
                                    @if ($page == $rows->currentPage())
                                        <span class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-indigo-50 text-sm font-medium text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300 dark:border-gray-600">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <button wire:click="gotoPage({{ $page }})" type="button" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring ring-blue-300 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                            {{ $page }}
                                        </button>
                                    @endif
                                @endif
                            @endforeach

                            @if ($end < $rows->lastPage())
                                @if ($end < $rows->lastPage() - 1)
                                    <span class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                                        ...
                                    </span>
                                @endif
                                <button wire:click="gotoPage({{ $rows->lastPage() }})" type="button" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                    {{ $rows->lastPage() }}
                                </button>
                            @endif

                            @if ($rows->hasMorePages())
                                <button wire:click="nextPage" wire:loading.attr="disabled" type="button" class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring ring-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:hover:text-gray-300">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @else
                                <span class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-default leading-5 dark:bg-gray-800 dark:border-gray-600">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            </nav>
        </div>
    @endif
</div>
