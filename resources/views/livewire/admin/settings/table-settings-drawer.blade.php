<div class="inline-block">
    {{-- 설정 버튼 --}}
    <button type="button" 
            wire:click="open"
            class="inline-flex items-center rounded-md bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
        <svg class="w-5 h-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        설정
    </button>

    {{-- Drawer 컴포넌트 (Livewire 전용, Alpine.js 없이) --}}
    @if($isOpen)
    <div class="relative z-50">
        {{-- 백드롭 --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
             wire:click="close"></div>
        
        {{-- Drawer 패널 --}}
        <div class="fixed inset-y-0 right-0 flex max-w-md">
            
            <div class="relative flex h-full w-screen max-w-md flex-col divide-y divide-gray-200 bg-white shadow-xl">
                {{-- 헤더 --}}
                <div class="h-0 flex-1 overflow-y-auto">
                    <div class="bg-gray-700 px-4 py-6 sm:px-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-semibold text-white">목록 페이지 설정</h2>
                            <div class="ml-3 flex h-7 items-center">
                                <button type="button" 
                                        wire:click="close"
                                        class="relative rounded-md text-gray-200 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                                    <span class="absolute -inset-2.5"></span>
                                    <span class="sr-only">설정 창 닫기</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                                        <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mt-1">
                            <p class="text-sm text-gray-300">목록 페이지의 표시 옵션과 기능을 설정할 수 있습니다.</p>
                        </div>
                    </div>
                    
                    {{-- 설정 폼 --}}
                    <div class="flex flex-1 flex-col justify-between">
                        <div class="divide-y divide-gray-200 px-4 sm:px-6">
                            {{-- 계층 구조 설정 --}}
                            <div class="space-y-6 pt-6 pb-5">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">계층 구조 표시</h3>
                                    <div class="mt-3">
                                        <div class="flex items-center">
                                            <input id="table-hierarchy-toggle" 
                                                   type="checkbox" 
                                                   wire:model.live="enableHierarchy"
                                                   class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-600">
                                            <label for="table-hierarchy-toggle" class="ml-3 text-sm text-gray-700">
                                                계층 구조로 표시 (부모-자식 관계를 트리 형태로 표시)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- 페이지당 항목 수 --}}
                                <div>
                                    <label for="table-per-page" class="block text-sm font-medium text-gray-900">페이지당 항목 수</label>
                                    <div class="mt-2">
                                        <select id="table-per-page" 
                                                wire:model.live="perPage"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm">
                                            <option value="10">10개</option>
                                            <option value="20">20개</option>
                                            <option value="50">50개</option>
                                            <option value="100">100개</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- 텍스트 길이 설정 --}}
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">텍스트 표시 길이</h3>
                                    <div class="mt-3 space-y-3">
                                        <div>
                                            <label for="table-title-length" class="block text-sm text-gray-700">제목 최대 길이</label>
                                            <input type="number" 
                                                   id="table-title-length" 
                                                   wire:model.live="titleLength"
                                                   min="10" 
                                                   max="100" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="table-description-length" class="block text-sm text-gray-700">설명 최대 길이</label>
                                            <input type="number" 
                                                   id="table-description-length" 
                                                   wire:model.live="descriptionLength"
                                                   min="10" 
                                                   max="200" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>

                                {{-- 기능 토글 --}}
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">기능 설정</h3>
                                    <div class="mt-3 space-y-2">
                                        <div class="flex items-center">
                                            <input id="table-enable-search" 
                                                   type="checkbox" 
                                                   wire:model.live="enableSearch"
                                                   class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-600">
                                            <label for="table-enable-search" class="ml-3 text-sm text-gray-700">검색 기능 활성화</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="table-enable-create" 
                                                   type="checkbox" 
                                                   wire:model.live="enableCreate"
                                                   class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-600">
                                            <label for="table-enable-create" class="ml-3 text-sm text-gray-700">생성 버튼 표시</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="table-enable-edit" 
                                                   type="checkbox" 
                                                   wire:model.live="enableEdit"
                                                   class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-600">
                                            <label for="table-enable-edit" class="ml-3 text-sm text-gray-700">수정 기능 활성화</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="table-enable-delete" 
                                                   type="checkbox" 
                                                   wire:model.live="enableDelete"
                                                   class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-600">
                                            <label for="table-enable-delete" class="ml-3 text-sm text-gray-700">삭제 기능 활성화</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="table-enable-bulk-actions" 
                                                   type="checkbox" 
                                                   wire:model.live="enableBulkActions"
                                                   class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-600">
                                            <label for="table-enable-bulk-actions" class="ml-3 text-sm text-gray-700">선택삭제 기능 활성화</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- 정렬 설정 --}}
                                <div>
                                    <label for="table-default-sort" class="block text-sm font-medium text-gray-900">기본 정렬</label>
                                    <div class="mt-2 grid grid-cols-2 gap-2">
                                        <select id="table-default-sort" 
                                                wire:model.live="defaultSort"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm">
                                            <option value="id">ID</option>
                                            <option value="title">제목</option>
                                            <option value="created_at">생성일</option>
                                            <option value="updated_at">수정일</option>
                                        </select>
                                        <select id="table-sort-direction" 
                                                wire:model.live="sortDirection"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm">
                                            <option value="asc">오름차순</option>
                                            <option value="desc">내림차순</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- 클릭 액션 설정 --}}
                                <div>
                                    <label for="table-clickable-column" class="block text-sm font-medium text-gray-900">클릭 가능한 컬럼</label>
                                    <div class="mt-2 grid grid-cols-2 gap-2">
                                        <select id="table-clickable-column" 
                                                wire:model.live="clickableColumn"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm">
                                            <option value="">선택 없음</option>
                                            <option value="id">ID</option>
                                            <option value="title">제목</option>
                                            <option value="description">설명</option>
                                            <option value="enable">상태</option>
                                            <option value="created_at">생성일</option>
                                            <option value="updated_at">수정일</option>
                                        </select>
                                        <select id="table-click-action" 
                                                wire:model.live="clickAction"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm">
                                            <option value="none">액션 없음</option>
                                            <option value="detail">상세보기</option>
                                            <option value="edit">수정하기</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- 푸터 버튼 --}}
                <div class="flex shrink-0 justify-end px-4 py-4">
                    <button type="button" 
                            wire:click="close"
                            class="rounded-md bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        취소
                    </button>
                    <button type="button" 
                            wire:click="save"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="ml-3 inline-flex justify-center rounded-md bg-gray-600 px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                        <span wire:loading.remove>설정 적용</span>
                        <span wire:loading wire:target="save">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            저장 중...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- 알림 처리 (Livewire 이벤트 사용) --}}
@script
<script>
    $wire.on('notify', (event) => {
        const detail = Array.isArray(event) ? event[0] : event;
        const { type, message } = detail;
        
        // 알림 생성
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-[60] p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' 
                ? 'bg-green-50 border border-green-200 text-green-800' 
                : 'bg-red-50 border border-red-200 text-red-800'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                ${type === 'success' 
                    ? '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                    : '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
                }
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // 3초 후 자동 제거
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    });
    
    $wire.on('refresh-page', () => {
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    });
</script>
@endscript