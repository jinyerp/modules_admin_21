@extends($jsonData['template']['layout'] ??'jiny-admin2::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">
    {{-- 헤더 컴포넌트로 교체 --}}
    @php
        $createRoute = isset($jsonData['currentRoute'])
            ? route($jsonData['currentRoute'] . '.create')
            : route('admin2.templates.create');
        $settingsPath = base_path('jiny/admin2/App/Http/Controllers/Admin/AdminTemplates/AdminTemplates.json');
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

    {{-- 검색 --}}
    @livewire('jiny-admin2::admin-search', [
        'jsonData' => $jsonData
    ])

    {{-- 선택 삭제 모달 --}}
    @livewire('jiny-admin2::admin-delete', [
        'jsonData' => $jsonData
    ])

    {{-- 템플릿 목록 테이블 컴포넌트 --}}
    @livewire('jiny-admin2::admin-table',[
        'jsonData' => $jsonData
    ])

    {{-- @php
        $controllerClass = isset($jsonData) && isset($jsonData['controller']) ? $jsonData['controller'] : null;
    @endphp
    @if($controllerClass)
        @livewire('jiny-admin2::admin-table', ['controller' => $controllerClass])
    @else
        @livewire('jiny-admin2::admin-table')
    @endif --}}
    
    {{-- Settings Drawer 컴포넌트 추가 --}}
    @livewire('jiny-admin2::settings.table-settings-drawer', [
        'jsonPath' => $settingsPath
    ])
</div>
@endsection
