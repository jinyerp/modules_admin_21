{{-- 
    템플릿 상세 보기 페이지
    선택한 관리자 템플릿의 상세 정보를 표시합니다.
    읽기 전용 뷰로 템플릿의 모든 필드 정보를 확인할 수 있습니다.
--}}
@extends($jsonData['template']['layout'] ??'jiny-admin2::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">
    {{-- 
        페이지 헤더 섹션
        페이지 제목, 설명, 목록으로 돌아가기 버튼을 표시
        상세 보기 모드에서 수정/삭제 버튼도 표시 가능
    --}}
    @php
        $indexRoute = isset($jsonData['currentRoute'])
            ? route($jsonData['currentRoute'] . '.index')
            : route('admin2.templates.index');
    @endphp
    
    {{-- JSON 설정 파일 경로 설정 --}}
    @php
        // 컨트롤러에서 전달받은 jsonPath 사용
        $settingsPath = $jsonPath ?? null;
    @endphp
    
    @livewire('jiny-admin2::admin-header-with-settings', [
        'data' => [
            'title' => $jsonData['show']['heading']['title'] ?? 'Template Details',
            'description' => $jsonData['show']['heading']['description'] ?? '템플릿 상세 정보',
            'routes' => [
                'list' => $indexRoute
            ]
        ],
        'mode' => 'show',
        'settingsPath' => $settingsPath
    ])

    {{-- 
        삭제 확인 모달 컴포넌트
        템플릿 삭제 시 확인 다이얼로그를 표시
        상세 보기 페이지에서 직접 삭제 가능
    --}}
    @livewire('jiny-admin2::admin-delete', [
        'jsonData' => $jsonData
    ])

    {{-- 
        템플릿 상세 정보 컴포넌트
        JSON 설정에 정의된 필드들을 읽기 전용 형태로 표시
        필드 레이블과 값을 깔끔한 레이아웃으로 렌더링
    --}}
    @livewire('jiny-admin2::admin-show', [
        'jsonData' => $jsonData,
        'data' => $data,
        'id' => $id
    ])
    
    {{-- 
        설정 드로어 컴포넌트
        관리자가 JSON 설정 파일을 실시간으로 편집할 수 있는 UI 제공
        상세 보기 페이지의 표시 필드를 동적으로 변경 가능
    --}}
    @livewire('jiny-admin2::settings.show-settings-drawer', [
        'jsonPath' => $settingsPath
    ])
</div>
@endsection
