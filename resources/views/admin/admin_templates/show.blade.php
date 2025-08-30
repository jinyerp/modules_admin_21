@extends($jsonData['template']['layout'] ??'jiny-admin2::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">
    {{-- 헤더 컴포넌트로 교체 --}}
    @php
        $indexRoute = isset($jsonData['currentRoute'])
            ? route($jsonData['currentRoute'] . '.index')
            : route('admin2.templates.index');
    @endphp
    
    @php
        $settingsPath = base_path('jiny/admin2/App/Http/Controllers/Admin/AdminTemplates/AdminTemplates.json');
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

    {{-- 삭제 모달 컴포넌트 --}}
    @livewire('jiny-admin2::admin-delete', [
        'jsonData' => $jsonData
    ])

    {{-- 상세 정보 Livewire 컴포넌트 --}}
    @livewire('jiny-admin2::admin-show', [
        'jsonData' => $jsonData,
        'data' => $data,
        'id' => $id
    ])
    
    {{-- Settings Drawer 컴포넌트 추가 (중복 제거) --}}
    @livewire('jiny-admin2::settings.show-settings-drawer', [
        'jsonPath' => $settingsPath
    ])
</div>
@endsection
