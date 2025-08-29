<div>
    @if($showModal)
    <!-- 모달 배경 -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 transition-opacity"
         wire:click="closeModal"></div>

    <!-- 모달 컨테이너 -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md border border-gray-200"
             @click.stop>
            <!-- 헤더 -->
            <div class="flex items-start gap-4 mb-6">
                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                        {{ $isBulkDelete ? '다중 항목 삭제 확인' : '삭제 확인' }}
                    </h3>
                    <div class="text-gray-700 mb-2">
                        @if($isBulkDelete)
                            선택한 <span class="font-bold text-red-600">{{ count($bulkItemIds) }}</span>개의 항목을 삭제하시겠습니까?
                        @else
                            @if($item)
                                <span class="font-bold">"{{ $item->title }}"</span> 항목을 삭제하시겠습니까?
                                <div class="text-sm text-gray-500 mt-1">
                                    ID: {{ $item->id }} 
                                    @if($itemId != $item->id)
                                        <span class="text-red-600">(경고: itemId={{ $itemId }}와 불일치)</span>
                                    @endif
                                </div>
                            @else
                                이 항목을 삭제하시겠습니까?
                                @if($itemId)
                                    <div class="text-sm text-red-500 mt-1">항목을 찾을 수 없음 (ID: {{ $itemId }})</div>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
                <button type="button"
                        wire:click="closeModal"
                        wire:loading.attr="disabled"
                        class="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <span wire:loading.remove wire:target="closeModal">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </span>
                    <span wire:loading wire:target="closeModal">
                        <svg class="animate-spin h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8m0 16a8 8 0 008-8m-8 8V4m8 8H4" />
                        </svg>
                    </span>
                </button>
            </div>

            <!-- 경고 메시지 -->
            @if(!$isBulkDelete && $descendantsCount > 0)
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-amber-800">주의</h4>
                        <div class="mt-1 text-sm text-amber-700">
                            이 항목은 <strong>{{ $descendantsCount }}개</strong>의 하위 항목을 포함하고 있습니다.
                            삭제 시 모든 하위 항목도 함께 삭제됩니다.
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-amber-800">경고</h4>
                        <div class="mt-1 text-sm text-amber-700">
                            이 작업은 취소할 수 없습니다. 삭제된 데이터는 복구할 수 없습니다.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 보안 키 확인 섹션 -->
            <div class="space-y-4 mb-6">
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-lg p-4">
                    <label class="block text-xs font-medium text-gray-600 mb-3 text-center">삭제를 확인하려면 아래 보안 키를 입력하세요</label>
                    <div class="flex items-center justify-center gap-2">
                        <span id="security-key-display"
                              class="text-lg font-bold text-gray-900 tracking-wider select-all cursor-pointer px-3 py-2 bg-white rounded-md border border-yellow-300"
                              onclick="selectText('security-key-display')"
                              title="클릭하여 선택">
                            {{ $securityKey }}
                        </span>
                        <button type="button"
                                wire:click="copyAndPasteSecurityKey"
                                class="p-2 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 rounded-md border border-gray-300 transition-all hover:shadow-sm"
                                title="복사">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                    @if($showCopySuccess)
                    <div class="mt-2 text-xs text-green-600 text-center" wire:transition.out.duration.1000ms>
                        <svg class="inline h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        복사되었습니다
                    </div>
                    @endif
                </div>

                <div>
                    <label for="confirmation-key" class="block text-sm font-medium text-gray-700 mb-2">
                        보안 키 입력
                    </label>
                    <input type="text"
                           id="confirmation-key"
                           wire:model.live="confirmationKey"
                           class="w-full px-3 py-2 border rounded-md transition-colors
                                  {{ $canDelete ? 'border-green-500 bg-green-50 focus:ring-green-500 focus:border-green-500' : 'border-gray-300 focus:ring-red-500 focus:border-red-500' }}"
                           placeholder="보안 키를 입력하세요"
                           autocomplete="off">
                    @if($confirmationKey && !$canDelete)
                    <div class="mt-2 text-xs text-red-600">
                        보안 키가 일치하지 않습니다
                    </div>
                    @elseif($canDelete)
                    <div class="mt-2 text-xs text-green-600">
                        <svg class="inline h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        보안 키가 일치합니다
                    </div>
                    @endif
                </div>
            </div>

            <!-- 버튼 -->
            <div class="flex justify-end gap-3">
                <button type="button"
                        wire:click="closeModal"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                    <span wire:loading.remove wire:target="closeModal">취소</span>
                    <span wire:loading wire:target="closeModal" class="flex items-center">
                        <svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        닫는 중...
                    </span>
                </button>
                <button type="button"
                        wire:click="confirmDelete"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        @if(!$canDelete) disabled @endif
                        class="px-4 py-2 rounded-lg transition-all font-medium
                               {{ $canDelete
                                  ? 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800 shadow-sm hover:shadow-md'
                                  : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}">
                    <span wire:loading.remove wire:target="confirmDelete" class="flex items-center justify-center">
                        @if($canDelete)
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        @else
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        @endif
                        @if($isBulkDelete)
                            {{ count($bulkItemIds) }}개 항목 삭제
                        @else
                            {{ $canDelete ? '삭제' : '삭제' }}
                        @endif
                    </span>
                    <span wire:loading wire:target="confirmDelete" class="flex items-center justify-center">
                        <svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        삭제 중...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- 스크립트 -->
    <script>
        function selectText(elementId) {
            const element = document.getElementById(elementId);
            if (window.getSelection && document.createRange) {
                const selection = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(element);
                selection.removeAllRanges();
                selection.addRange(range);
            }
        }

        // 클립보드 복사 이벤트 리스너
        window.addEventListener('copy-to-clipboard', event => {
            const text = event.detail.text;
            navigator.clipboard.writeText(text).then(() => {
                console.log('텍스트가 클립보드에 복사되었습니다:', text);
                // 1.5초 후 복사 성공 메시지 숨기기
                setTimeout(() => {
                    @this.call('hideCopySuccess');
                }, 1500);
            }).catch(err => {
                console.error('클립보드 복사 실패:', err);
            });
        });

        // 확인 키 업데이트 이벤트 리스너
        window.addEventListener('update-confirmation-key', event => {
            const key = event.detail.key;
            const input = document.getElementById('confirmation-key');
            if (input) {
                input.value = key;
                // Livewire에 값 변경 알림
                input.dispatchEvent(new Event('input'));
            }
        });
    </script>
</div>
