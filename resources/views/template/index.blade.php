{{-- 
    템플릿 목록 페이지
    관리자 템플릿의 전체 목록을 테이블 형태로 표시합니다.
    검색, 필터링, 페이지네이션, 선택 삭제 기능을 제공합니다.
--}}
@extends($jsonData['template']['layout'] ??'jiny-admin2::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">
    {{-- 
        페이지 헤더 섹션
        페이지 제목, 설명, 새 템플릿 생성 버튼을 표시
        설정 드로어 활성화 버튼 포함
    --}}
    @php
        $createRoute = isset($jsonData['currentRoute'])
            ? route($jsonData['currentRoute'] . '.create')
            : route('admin2.templates.create');
        // 컨트롤러에서 전달받은 jsonPath 사용
        $settingsPath = $jsonPath ?? null;
    @endphp
    
    @livewire('jiny-admin2::admin-header-with-settings', [
        'data' => [
            'title' => $jsonData['index']['heading']['title'] ?? 'Admin Templates',
            'description' => $jsonData['index']['heading']['description'] ?? '템플릿 목록을 관리합니다.',
            'routes' => [
                'create' => $createRoute,
                'list' => isset($jsonData['currentRoute']) ? route($jsonData['currentRoute'] . '.index') : route('admin2.templates.index')
            ]
        ],
        'mode' => 'index',
        'settingsPath' => $settingsPath
    ])

    {{-- 
        검색 컴포넌트
        템플릿 목록을 검색할 수 있는 입력 필드 제공
        실시간 검색 및 필터링 기능 지원
    --}}
    @livewire('jiny-admin2::admin-search', [
        'jsonData' => $jsonData
    ])

    {{-- 
        선택 삭제 모달 컴포넌트
        체크박스로 선택한 여러 템플릿을 한번에 삭제
        삭제 전 확인 다이얼로그 표시
    --}}
    @livewire('jiny-admin2::admin-delete', [
        'jsonData' => $jsonData
    ])

    {{-- 
        템플릿 목록 테이블 컴포넌트
        JSON 설정에 정의된 커럼에 따라 동적으로 테이블 생성
        페이지네이션, 정렬, 필터링 기능 포함
        각 행에 수정/삭제 버튼 표시
    --}}
    @livewire('jiny-admin2::admin-table',[
        'jsonData' => $jsonData
    ])

    {{-- 
        주석 처리된 코드: 컨트롤러 클래스를 직접 전달하는 대체 방법
        현재는 jsonData를 통해 컨트롤러 정보를 전달하는 방식 사용
    --}}
    
    {{-- 
        설정 드로어 컴포넌트
        관리자가 JSON 설정 파일을 실시간으로 편집할 수 있는 UI 제공
        테이블 커럼, 검색 필드, 페이지네이션 설정 등을 동적으로 변경 가능
    --}}
    @livewire('jiny-admin2::settings.table-settings-drawer', [
        'jsonPath' => $settingsPath
    ])
</div>
@endsection
