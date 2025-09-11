@extends($jsonData['template']['layout'] ?? 'jiny-admin::layouts.admin')

@section('content')
<div class="min-h-screen">
    <!-- 헤더 -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                        <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-500">마지막 업데이트: {{ now()->format('H:i:s') }}</span>
                        <button onclick="location.reload()" class="p-1.5 rounded hover:bg-gray-100">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    {{-- <div class="bg-white shadow-sm border-b">

    </div> --}}

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- 주요 통계 카드 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- 전체 사용자 -->
            <a href="{{ route('admin.users') }}" class="block">
                <div class="bg-white rounded p-6 shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] hover:shadow-md transition-all duration-300 cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-600 mb-3">전체 사용자</h3>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                            <p class="text-xs text-gray-500 mt-2">관리자: {{ $stats['admin_users'] }}명</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 활성 세션 -->
            <a href="{{ route('admin.user.sessions') }}" class="block">
                <div class="bg-white rounded p-6 shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] hover:shadow-md transition-all duration-300 cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-600 mb-3">활성 세션</h3>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['active_sessions']) }}</p>
                            <p class="text-xs text-green-600 mt-2">현재 접속 중</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 오늘 로그인 -->
            <a href="{{ route('admin.user.logs') }}" class="block">
                <div class="bg-white rounded p-6 shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] hover:shadow-md transition-all duration-300 cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-600 mb-3">오늘 로그인</h3>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['today_logins']) }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ now()->format('Y-m-d') }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-100 rounded flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 보안 상태 -->
            <a href="{{ route('admin.user.2fa') }}" class="block">
                <div class="bg-white rounded p-6 shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] hover:shadow-md transition-all duration-300 cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-600 mb-3">2FA 사용률</h3>
                            <p class="text-xl font-bold text-gray-900">{{ $security['two_factor_percentage'] }}%</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $security['two_factor_enabled'] }}/{{ $stats['total_users'] }}명</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 왼쪽: 최근 활동 -->
            <div class="lg:col-span-2 space-y-6">
                <!-- 로그인 트렌드 차트 -->
                <div class="bg-white rounded shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">24시간 로그인 트렌드</h3>
                    <p class="text-sm text-gray-500 mb-4">시간대별 로그인 현황을 차트로 표시합니다</p>
                    <div class="h-48">
                        <canvas id="loginTrendChart"></canvas>
                    </div>
                </div>

                <!-- 최근 활동 -->
                <div class="bg-white rounded shadow-[0_1px_3px_0_rgba(0,0,0,0.05)]">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">최근 활동</h3>
                            <p class="text-sm text-gray-500 mt-1">사용자들의 최근 활동 내역을 확인할 수 있습니다</p>
                        </div>
                        <a href="{{ route('admin.user.logs') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            전체 보기 →
                        </a>
                    </div>
                    <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                        @forelse($recent_activities as $activity)
                        <div class="px-4 py-3 hover:bg-gray-50">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="p-1.5 bg-{{ $activity['color'] }}-100 rounded-full">
                                        <svg class="w-3.5 h-3.5 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activity['icon'] }}"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs text-gray-900">
                                            @if($activity['user_id'])
                                                <a href="{{ route('admin.users.show', $activity['user_id']) }}" class="font-medium hover:text-indigo-600">
                                                    {{ $activity['name'] ?? $activity['email'] }}
                                                </a>
                                            @else
                                                <span class="font-medium">{{ $activity['email'] }}</span>
                                            @endif
                                            <span class="ml-1 text-gray-500">{{ $activity['label'] }}</span>
                                        </p>
                                        <span class="text-xs text-gray-400">
                                            {{ $activity['logged_at']->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="mt-1 flex items-center space-x-2">
                                        <span class="text-xs text-gray-500">IP: {{ $activity['ip_address'] }}</span>
                                        @if($activity['browser'])
                                        <span class="text-xs text-gray-400">•</span>
                                        <span class="text-xs text-gray-500">{{ $activity['browser'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-4 py-8 text-center">
                            <p class="text-xs text-gray-500">활동 기록이 없습니다</p>
                        </div>
                        @endforelse
                    </div>
                </div>


            </div>

            <!-- 오른쪽: 세션 및 보안 정보 -->
            <div class="space-y-6">
                <!-- 활성 세션 -->
                <div class="bg-white rounded shadow-[0_1px_3px_0_rgba(0,0,0,0.05)]">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">활성 세션</h3>
                            {{-- <p class="text-sm text-gray-500 mt-1">현재 접속 중인 사용자 세션 목록</p> --}}
                        </div>
                        <a href="{{ route('admin.user.sessions') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            전체 보기 →
                        </a>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($active_sessions as $session)
                        <div class="px-4 py-3 hover:bg-gray-50">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-medium text-gray-900">
                                    {{ Str::limit($session['email'], 20) }}
                                </p>
                                @if($session['is_current'])
                                <span class="text-xs px-1.5 py-0.5 bg-green-100 text-green-700 rounded">현재</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">{{ $session['browser'] }}</p>
                            <p class="text-xs text-gray-400">{{ $session['ip_address'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $session['last_activity'] ? $session['last_activity']->diffForHumans() : '활동 없음' }}
                            </p>
                        </div>
                        @empty
                        <div class="px-4 py-8 text-center">
                            <p class="text-xs text-gray-500">활성 세션이 없습니다</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- 보안 상태 -->
                <div class="bg-white rounded shadow-[0_1px_3px_0_rgba(0,0,0,0.05)]">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">보안 상태</h3>
                        {{-- <p class="text-sm text-gray-500 mt-1">시스템 보안 관련 주요 지표</p> --}}
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">차단된 IP</span>
                            <span class="text-sm font-semibold text-red-600">{{ $security['blocked_ips'] }}개</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">오늘 실패 시도</span>
                            <span class="text-sm font-semibold text-yellow-600">{{ $security['failed_attempts_today'] }}회</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">2FA 사용자</span>
                            <span class="text-sm font-semibold text-green-600">{{ $security['two_factor_enabled'] }}명</span>
                        </div>
                        <div class="pt-4 mt-4 border-t border-gray-100 space-y-2">
                            @if($security['blocked_ips'] > 0)
                            <a href="{{ route('admin.user.password.logs') }}?status=blocked" class="block text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                차단 목록 보기 →
                            </a>
                            @endif
                            <a href="{{ route('admin.captcha.logs') }}" class="block text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                CAPTCHA 로그 보기 →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 브라우저 통계 -->
                <div class="bg-white rounded shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">브라우저 사용 현황</h3>
                    {{-- <p class="text-sm text-gray-500 mb-4">사용자들이 사용하는 브라우저 통계</p> --}}
                    <div class="h-32">
                        <canvas id="browserChart"></canvas>
                    </div>
                </div>

                <!-- 시스템 정보 -->
                <div class="bg-white rounded shadow-[0_1px_3px_0_rgba(0,0,0,0.05)]">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">시스템 정보</h3>
                        {{-- <p class="text-sm text-gray-500 mt-1">현재 시스템 환경 및 버전 정보</p> --}}
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">환경</span>
                            <span class="text-sm font-medium text-gray-800">{{ $system_status['environment'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">PHP</span>
                            <span class="text-sm font-medium text-gray-800">{{ $system_status['php_version'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Laravel</span>
                            <span class="text-sm font-medium text-gray-800">{{ $system_status['laravel_version'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">시간대</span>
                            <span class="text-sm font-medium text-gray-800">{{ $system_status['timezone'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">디버그</span>
                            <span class="text-sm font-medium {{ $system_status['debug_mode'] === 'On' ? 'text-yellow-600' : 'text-gray-800' }}">
                                {{ $system_status['debug_mode'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 최근 차단 -->
        @if($recent_blocks->count() > 0)
        <div class="mt-6 bg-white rounded shadow-[0_1px_3px_0_rgba(0,0,0,0.05)]">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">최근 차단된 IP</h3>
                    <p class="text-sm text-gray-500 mt-1">비정상적인 로그인 시도로 차단된 IP 목록</p>
                </div>
                <a href="{{ route('admin.user.password.logs') }}?status=blocked" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    전체 보기 →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">이메일</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">IP 주소</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">시도 횟수</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">차단 시간</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">브라우저</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recent_blocks as $block)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-xs text-gray-900">{{ $block['email'] }}</td>
                            <td class="px-4 py-2 text-xs text-gray-500">{{ $block['ip_address'] }}</td>
                            <td class="px-4 py-2">
                                <span class="px-1.5 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded">
                                    {{ $block['attempt_count'] }}회
                                </span>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500">
                                {{ $block['blocked_at']->format('m-d H:i') }}
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500">{{ $block['browser'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 로그인 트렌드 차트
    const loginCtx = document.getElementById('loginTrendChart').getContext('2d');
    const loginChart = new Chart(loginCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($login_trend['labels']) !!},
            datasets: [{
                label: '로그인',
                data: {!! json_encode($login_trend['data']) !!},
                borderColor: 'rgb(79, 70, 229)',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 }
                    }
                },
                x: {
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });

    // 브라우저 통계 차트
    const browserCtx = document.getElementById('browserChart').getContext('2d');
    new Chart(browserCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($browser_stats['labels']) !!},
            datasets: [{
                data: {!! json_encode($browser_stats['data']) !!},
                backgroundColor: [
                    'rgba(79, 70, 229, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(244, 63, 94, 0.8)',
                    'rgba(156, 163, 175, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { font: { size: 10 } }
                }
            }
        }
    });

    // 차트 데이터 업데이트 함수
    function updateLoginChart() {
        fetch('{{ route("admin.dashboard") }}?ajax=1')
            .then(response => response.json())
            .then(data => {
                if (data.login_trend) {
                    loginChart.data.labels = data.login_trend.labels;
                    loginChart.data.datasets[0].data = data.login_trend.data;
                    loginChart.update();
                }
            })
            .catch(error => console.error('Error updating chart:', error));
    }

    // 30초마다 차트 업데이트 (페이지 전체 새로고침 없이)
    setInterval(updateLoginChart, 30000);

    // 자동 페이지 새로고침 (5분마다)
    @if($jsonData['refresh']['enabled'] ?? true)
    setTimeout(function() {
        location.reload();
    }, {{ $jsonData['refresh']['interval'] ?? 300000 }});
    @endif
});
</script>
@endpush
@endsection
