@extends($jsonData['template']['layout'] ??'jiny-admin2::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">
    {{-- 헤더 컴포넌트로 교체 --}}
    @php
        $indexRoute = isset($jsonData['currentRoute'])
            ? route($jsonData['currentRoute'] . '.index')
            : route('admin2.templates.index');
        $settingsPath = base_path('jiny/admin2/App/Http/Controllers/Admin/AdminTemplates/AdminTemplates.json');
    @endphp
    
    @livewire('jiny-admin2::admin-header-with-settings', [
        'data' => [
            'title' => $jsonData['create']['heading']['title'] ?? 'Create New Template',
            'description' => $jsonData['create']['heading']['description'] ?? '새로운 템플릿을 생성합니다.',
            'routes' => [
                'list' => $indexRoute
            ]
        ],
        'mode' => 'create',
        'settingsPath' => $settingsPath
    ])

    {{-- 생성 폼 Livewire 컴포넌트 --}}
    @livewire('jiny-admin2::admin-create', [
        'jsonData' => $jsonData,
        'form' => $form
    ])
    
    {{-- Settings Drawer 컴포넌트 추가 --}}
    @livewire('jiny-admin2::settings.create-settings-drawer', [
        'jsonPath' => $settingsPath
    ])
</div>

{{-- JavaScript for handling redirect with history replacement --}}
<script>
    window.addEventListener('redirect-with-replace', event => {
        window.location.replace(event.detail.url);
    });
</script>
@endsection
