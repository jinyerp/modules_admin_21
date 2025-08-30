@extends($jsonData['template']['layout'] ??'jiny-admin2::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">
    {{-- 헤더 컴포넌트로 교체 --}}
    @php
        $indexRoute = isset($jsonData['currentRoute'])
            ? route($jsonData['currentRoute'] . '.index')
            : url()->previous();
        $settingsPath = base_path('jiny/admin2/App/Http/Controllers/Admin/AdminTemplates/AdminTemplates.json');
    @endphp
    
    @livewire('jiny-admin2::admin-header-with-settings', [
        'data' => array_merge($jsonData, [
            'title' => $jsonData['edit']['heading']['title'] ?? 'Edit Template',
            'description' => $jsonData['edit']['heading']['description'] ?? '템플릿을 수정합니다.',
            'routes' => [
                'list' => $indexRoute
            ]
        ]),
        'mode' => 'edit',
        'settingsPath' => $settingsPath
    ])

    {{-- 삭제 모달 컴포넌트 --}}
    @livewire('jiny-admin2::admin-delete', [
        'jsonData' => $jsonData
    ])

    {{-- 수정 폼 Livewire 컴포넌트 --}}
    @livewire('jiny-admin2::admin-edit', [
        'jsonData' => $jsonData,
        'form' => $form,
        'id' => $id
    ])
    
    {{-- Settings Drawer 컴포넌트 추가 --}}
    @livewire('jiny-admin2::settings.edit-settings-drawer', [
        'jsonPath' => $settingsPath
    ])
</div>

{{-- JavaScript for handling redirect with history replacement --}}
<script>
    window.addEventListener('redirect-with-replace', event => {
        window.location.replace(event.detail.url);
    });
    
    // 설정 저장 후 페이지 새로고침
    window.addEventListener('refresh-page', event => {
        window.location.reload();
    });
</script>
@endsection
