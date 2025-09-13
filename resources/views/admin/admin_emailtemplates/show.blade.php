<div>
    <h2 class="text-lg font-semibold text-gray-900 mb-4">이메일 템플릿 상세</h2>
    
    <div class="space-y-6">
        {{-- 기본 정보 --}}
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-4 py-3 bg-gray-50 border-b">
                <h3 class="text-sm font-medium text-gray-900">기본 정보</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">ID</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $data['id'] ?? '' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-xs font-medium text-gray-500">템플릿 이름</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $data['name'] ?? '' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-xs font-medium text-gray-500">슬러그</dt>
                        <dd class="text-sm text-gray-900 mt-1">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $data['slug'] ?? '' }}</code>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-xs font-medium text-gray-500">상태</dt>
                        <dd class="mt-1">
                            @if($data['is_active'] ?? false)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    활성
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    비활성
                                </span>
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-xs font-medium text-gray-500">타입</dt>
                        <dd class="text-sm text-gray-900 mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ strtoupper($data['type'] ?? 'html') }}
                            </span>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-xs font-medium text-gray-500">생성일</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $data['created_at'] ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- 템플릿 내용 --}}
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-4 py-3 bg-gray-50 border-b">
                <h3 class="text-sm font-medium text-gray-900">템플릿 내용</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">제목</label>
                    <div class="bg-gray-50 p-3 rounded border border-gray-200">
                        <code class="text-xs">{{ $data['subject'] ?? '' }}</code>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">본문</label>
                    <div class="bg-gray-50 p-3 rounded border border-gray-200 max-h-64 overflow-auto">
                        <pre class="text-xs whitespace-pre-wrap">{{ $data['body'] ?? '' }}</pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- 사용 가능한 변수 --}}
        @if(!empty($data['variables']))
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-4 py-3 bg-gray-50 border-b">
                <h3 class="text-sm font-medium text-gray-900">사용 가능한 변수</h3>
            </div>
            <div class="p-6">
                @php
                    $variables = is_string($data['variables']) ? json_decode($data['variables'], true) : $data['variables'];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                    @foreach($variables ?? [] as $variable)
                        <code class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded border border-blue-200">
                            {{ "{{" . $variable . "}}" }}
                        </code>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- 미리보기 및 테스트 --}}
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-4 py-3 bg-gray-50 border-b">
                <h3 class="text-sm font-medium text-gray-900">미리보기 및 테스트</h3>
            </div>
            <div class="p-6">
                <div class="flex space-x-3">
                    <button 
                        type="button"
                        onclick="previewTemplate({{ $data['id'] }})"
                        class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        미리보기
                    </button>
                    
                    <button 
                        type="button"
                        onclick="openTestModal({{ $data['id'] }})"
                        class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        테스트 발송
                    </button>
                </div>
            </div>
        </div>

        {{-- 미리보기 모달 --}}
        <div id="previewModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
                        <h3 class="text-sm font-medium text-gray-900">템플릿 미리보기</h3>
                        <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4 overflow-auto" style="max-height: calc(90vh - 120px);">
                        <iframe id="previewFrame" class="w-full h-full min-h-[600px] border-0"></iframe>
                    </div>
                </div>
            </div>
        </div>

        {{-- 테스트 발송 모달 --}}
        <div id="testModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-lg max-w-md w-full">
                    <div class="px-4 py-3 bg-gray-50 border-b">
                        <h3 class="text-sm font-medium text-gray-900">테스트 이메일 발송</h3>
                    </div>
                    <div class="p-4">
                        <label for="testEmail" class="block text-xs font-medium text-gray-700 mb-1">
                            수신 이메일 주소
                        </label>
                        <input 
                            type="email" 
                            id="testEmail" 
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="test@example.com"
                        >
                        <p class="mt-1 text-xs text-gray-500">테스트 이메일을 받을 주소를 입력하세요.</p>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 border-t flex justify-end space-x-2">
                        <button 
                            onclick="closeTestModal()"
                            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                        >
                            취소
                        </button>
                        <button 
                            onclick="sendTestEmail()"
                            class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                        >
                            발송
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentTemplateId = null;

function previewTemplate(templateId) {
    fetch(`/admin/emailtemplates/preview/${templateId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.preview) {
                const frame = document.getElementById('previewFrame');
                frame.srcdoc = data.preview.body;
                document.getElementById('previewModal').classList.remove('hidden');
            } else {
                alert(data.error || '미리보기를 생성할 수 없습니다.');
            }
        })
        .catch(error => {
            console.error('Preview error:', error);
            alert('미리보기 생성 중 오류가 발생했습니다.');
        });
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
}

function openTestModal(templateId) {
    currentTemplateId = templateId;
    document.getElementById('testModal').classList.remove('hidden');
}

function closeTestModal() {
    document.getElementById('testModal').classList.add('hidden');
    document.getElementById('testEmail').value = '';
    currentTemplateId = null;
}

function sendTestEmail() {
    const email = document.getElementById('testEmail').value;
    if (!email) {
        alert('이메일 주소를 입력하세요.');
        return;
    }

    fetch(`/admin/emailtemplates/test-send`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            id: currentTemplateId,
            email: email
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || '테스트 이메일이 발송되었습니다.');
            closeTestModal();
        } else {
            alert(data.error || '테스트 발송에 실패했습니다.');
        }
    })
    .catch(error => {
        console.error('Test send error:', error);
        alert('테스트 발송 중 오류가 발생했습니다.');
    });
}

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreviewModal();
        closeTestModal();
    }
});
</script>