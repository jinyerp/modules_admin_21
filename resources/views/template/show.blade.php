{{-- 
    템플릿 상세 보기 페이지
    선택한 관리자 템플릿의 상세 정보를 표시합니다.
    읽기 전용 뷰로 템플릿의 모든 필드 정보를 확인할 수 있습니다.
--}}
@extends($jsonData['template']['layout'] ??'jiny-admin::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">
    {{-- 오류 발생 시 표시할 컨테이너 --}}
    @if(session('livewire_error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">컴포넌트 로딩 오류</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>{{ session('livewire_error') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 
        페이지 헤더 섹션
        페이지 제목, 설명, 목록으로 돌아가기 버튼을 표시
        상세 보기 모드에서 수정/삭제 버튼도 표시 가능
    --}}
    @php
        try {
    @endphp
        @livewire('jiny-admin::admin-header-with-settings', [
            'jsonData' => $jsonData,
            'jsonPath' => $jsonPath ?? null,
            'mode' => 'show'
        ])
    @php
        } catch (\Exception $e) {
            echo '<div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">';
            echo '<div class="text-sm text-yellow-800">';
            echo '<strong>헤더 컴포넌트 오류:</strong> ' . $e->getMessage();
            echo '</div></div>';
        }
    @endphp

    {{-- 
        삭제 확인 모달 컴포넌트
        템플릿 삭제 시 확인 다이얼로그를 표시
        상세 보기 페이지에서 직접 삭제 가능
    --}}
    @php
        try {
    @endphp
        @livewire('jiny-admin::admin-delete', [
            'jsonData' => $jsonData
        ])
    @php
        } catch (\Exception $e) {
            echo '<div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">';
            echo '<div class="text-sm text-yellow-800">';
            echo '<strong>삭제 컴포넌트 오류:</strong> ' . $e->getMessage();
            echo '</div></div>';
        }
    @endphp

    {{-- 
        템플릿 상세 정보 컴포넌트
        JSON 설정에 정의된 필드들을 읽기 전용 형태로 표시
        필드 레이블과 값을 깔끔한 레이아웃으로 렌더링
    --}}
    @php
        try {
    @endphp
        @livewire('jiny-admin::admin-show', [
            'jsonData' => $jsonData,
            'data' => $data,
            'id' => $id,
            'controllerClass' => $controllerClass ?? null
        ])
    @php
        } catch (\Exception $e) {
            echo '<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">';
            echo '<div class="text-sm text-red-800">';
            echo '<strong>상세보기 컴포넌트 오류:</strong> ' . $e->getMessage();
            echo '</div>';
            echo '<div class="mt-2 text-xs text-red-600">';
            echo '<strong>스택 트레이스:</strong><br>';
            echo nl2br(htmlspecialchars($e->getTraceAsString()));
            echo '</div></div>';
            
            // 데이터 디버깅 정보
            echo '<div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">';
            echo '<div class="text-sm text-gray-800">';
            echo '<strong>디버그 정보:</strong><br>';
            echo 'Controller Class: ' . ($controllerClass ?? 'null') . '<br>';
            echo 'ID: ' . ($id ?? 'null') . '<br>';
            echo 'Data Keys: ' . (isset($data) ? implode(', ', array_keys($data)) : 'null') . '<br>';
            echo 'JsonData Keys: ' . (isset($jsonData) ? implode(', ', array_keys($jsonData)) : 'null');
            echo '</div></div>';
        }
    @endphp
    
    {{-- 
        설정 드로어 컴포넌트
        관리자가 JSON 설정 파일을 실시간으로 편집할 수 있는 UI 제공
        상세 보기 페이지의 표시 필드를 동적으로 변경 가능
    --}}
    @php
        try {
    @endphp
        @livewire('jiny-admin::settings.show-settings-drawer', [
            'jsonPath' => $settingsPath
        ])
    @php
        } catch (\Exception $e) {
            echo '<div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">';
            echo '<div class="text-sm text-yellow-800">';
            echo '<strong>설정 드로어 컴포넌트 오류:</strong> ' . $e->getMessage();
            echo '</div></div>';
        }
    @endphp
</div>
@endsection
