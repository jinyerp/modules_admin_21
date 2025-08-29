<div class="inline-block">
    {{-- 설정 버튼 --}}
    <button type="button"
            wire:click="open" 
            class="inline-flex items-center rounded-md bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
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
                    <div class="bg-orange-700 px-4 py-6 sm:px-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-semibold text-white">수정 페이지 설정</h2>
                            <div class="ml-3 flex h-7 items-center">
                                <button type="button" 
                                        wire:click="close"
                                        class="relative rounded-md text-orange-200 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                                    <span class="absolute -inset-2.5"></span>
                                    <span class="sr-only">설정 창 닫기</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                                        <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mt-1">
                            <p class="text-sm text-orange-300">수정 페이지 표시 옵션과 기능을 설정할 수 있습니다.</p>
                        </div>
                    </div>
                    
                    {{-- 설정 폼 --}}
                    <div class="flex flex-1 flex-col justify-between">
                        <div class="divide-y divide-gray-200 px-4 sm:px-6">
                            {{-- 기능 설정 섹션 --}}
                            <div class="space-y-6 pt-6 pb-5">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">기능 설정</h3>
                                    <div class="mt-3 space-y-3">
                                        <div class="flex items-center">
                                            <input id="edit-enable-delete" 
                                                   type="checkbox" 
                                                   wire:model.live="enableDelete"
                                                   class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-600">
                                            <label for="edit-enable-delete" class="ml-3 text-sm text-gray-700">삭제 기능</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="edit-enable-list-button" 
                                                   type="checkbox" 
                                                   wire:model.live="enableListButton"
                                                   class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-600">
                                            <label for="edit-enable-list-button" class="ml-3 text-sm text-gray-700">목록 버튼 표시</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="edit-enable-detail-button" 
                                                   type="checkbox" 
                                                   wire:model.live="enableDetailButton"
                                                   class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-600">
                                            <label for="edit-enable-detail-button" class="ml-3 text-sm text-gray-700">상세 보기 버튼 표시</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="edit-enable-add-child" 
                                                   type="checkbox" 
                                                   wire:model.live="enableAddChild"
                                                   class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-600">
                                            <label for="edit-enable-add-child" class="ml-3 text-sm text-gray-700">자식 항목 추가 버튼</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="edit-enable-parent-navigation" 
                                                   type="checkbox" 
                                                   wire:model.live="enableParentNavigation"
                                                   class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-600">
                                            <label for="edit-enable-parent-navigation" class="ml-3 text-sm text-gray-700">상위 탐색 표시</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="edit-enable-hierarchy" 
                                                   type="checkbox" 
                                                   wire:model.live="enableHierarchy"
                                                   class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-600">
                                            <label for="edit-enable-hierarchy" class="ml-3 text-sm text-gray-700">계층 구조 활성화</label>
                                        </div>
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
                            class="ml-3 inline-flex justify-center rounded-md bg-orange-600 px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600">
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