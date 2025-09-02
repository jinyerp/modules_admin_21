{{-- 사용자 상세 정보 페이지 --}}

{{-- 헤더 섹션 --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">사용자 상세 정보</h2>
                <p class="mt-1 text-xs text-gray-600">사용자의 계정 정보 및 활동 내역을 확인할 수 있습니다</p>
            </div>
            <div class="flex items-center space-x-3">
                {{-- 이메일 인증 상태 --}}
                @if($data['email_verified_at'] ?? false)
                    <span class="inline-flex items-center h-6 px-2.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        이메일 인증됨
                    </span>
                @else
                    <span class="inline-flex items-center h-6 px-2.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        이메일 미인증
                    </span>
                @endif
                
                {{-- 관리자 권한 상태 --}}
                @if($data['isAdmin'] ?? false)
                    <span class="inline-flex items-center h-6 px-2.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        관리자
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- 기본 정보 --}}
    <div class="px-6 py-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- 왼쪽 컬럼 --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">사용자 ID</label>
                    <p class="text-sm font-mono text-gray-900">#{{ $data['id'] ?? '-' }}</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">이름</label>
                    <p class="text-sm font-semibold text-gray-900">{{ $data['name'] ?? '-' }}</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">이메일</label>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <p class="text-sm text-gray-900">{{ $data['email'] ?? '-' }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">이메일 인증일</label>
                    <p class="text-sm text-gray-900">
                        @if($data['email_verified_at'] ?? false)
                            {{ \Carbon\Carbon::parse($data['email_verified_at'])->format('Y년 m월 d일 H:i') }}
                        @else
                            <span class="text-gray-400">미인증</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- 오른쪽 컬럼 --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">
                        <a href="{{ route('admin.user.type') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
                            사용자 유형
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </label>
                    <div class="flex items-center space-x-2">
                        @if(isset($data['utype_name']) && $data['utype_name'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $data['utype_name'] }}
                            </span>
                            @if($data['utype'])
                                <span class="text-xs text-gray-500">({{ $data['utype'] }})</span>
                            @endif
                        @elseif($data['utype'] ?? false)
                            <span class="text-sm text-gray-900">{{ $data['utype'] }}</span>
                        @else
                            <span class="text-sm text-gray-400">-</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">관리자 권한</label>
                    <p class="text-sm text-gray-900">
                        @if($data['isAdmin'] ?? false)
                            <span class="text-purple-600 font-medium">관리자</span>
                        @else
                            <span class="text-gray-600">일반 사용자</span>
                        @endif
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">
                        <a href="{{ route('admin.user.logs') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
                            마지막 로그인
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </label>
                    <div class="text-sm text-gray-900">
                        @if($data['last_login_at'] ?? false)
                            <a href="{{ route('admin.user.logs', ['user_id' => $data['id']]) }}" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                                {{ \Carbon\Carbon::parse($data['last_login_at'])->format('Y년 m월 d일 H:i') }}
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                            <span class="text-xs text-gray-500 ml-1">({{ \Carbon\Carbon::parse($data['last_login_at'])->diffForHumans() }})</span>
                        @else
                            <span class="text-gray-400">기록 없음</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">계정 상태</label>
                    <div class="flex items-center">
                        @if($data['deleted_at'] ?? false)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                삭제됨
                            </span>
                        @elseif($data['is_active'] ?? true)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                활성
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                비활성
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 추가 정보 섹션 --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900">추가 정보</h3>
    </div>
    <div class="px-6 py-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- 타임스탬프 정보 --}}
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">가입일</p>
                        <p class="text-xs text-gray-900">
                            @if($data['created_at'] ?? false)
                                {{ \Carbon\Carbon::parse($data['created_at'])->format('Y년 m월 d일 H:i:s') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">최근 수정일</p>
                        <p class="text-xs text-gray-900">
                            @if($data['updated_at'] ?? false)
                                {{ \Carbon\Carbon::parse($data['updated_at'])->format('Y년 m월 d일 H:i:s') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- 보안 정보 --}}
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Remember Token</label>
                    <p class="text-xs text-gray-600 font-mono break-all">
                        @if($data['remember_token'] ?? false)
                            {{ substr($data['remember_token'], 0, 20) }}...
                        @else
                            <span class="text-gray-400">없음</span>
                        @endif
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">비밀번호 변경</label>
                    <p class="text-xs text-gray-600">
                        @if($data['password_changed_at'] ?? false)
                            {{ \Carbon\Carbon::parse($data['password_changed_at'])->diffForHumans() }}
                        @else
                            <span class="text-gray-400">변경 기록 없음</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 활동 로그 섹션 (선택적) --}}
@if(isset($logs) && count($logs) > 0)
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900">최근 활동 로그</h3>
    </div>
    <div class="px-6 py-4">
        <div class="space-y-3">
            @foreach($logs->take(5) as $log)
            <div class="flex items-start space-x-3 pb-3 border-b border-gray-100 last:border-0">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-900">{{ $log->action }}</p>
                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif