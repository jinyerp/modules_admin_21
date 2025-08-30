{{--
    템플릿 생성 페이지
    새로운 관리자 템플릿을 생성하는 폼을 표시합니다.
    JSON 설정 파일을 기반으로 동적으로 폼 필드를 생성합니다.
--}}
@extends($jsonData['template']['layout'] ??'jiny-admin2::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">

    {{--
        페이지 헤더 섹션
        페이지 제목, 설명, 목록으로 돌아가기 버튼을 표시
    --}}
    @php
        $indexRoute = isset($jsonData['currentRoute'])
            ? route($jsonData['currentRoute'] . '.index')
            : route('admin2.templates.index');
        // 컨트롤러에서 전달받은 jsonPath 사용
        $settingsPath = $jsonPath ?? null;
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

    {{--
        템플릿 생성 폼 컴포넌트
        JSON 설정에 정의된 필드들을 동적으로 렌더링
        폼 유효성 검사 및 데이터 저장 처리
    --}}
    @livewire('jiny-admin2::admin-create', [
        'jsonData' => $jsonData,
        'form' => $form
    ])

    {{--
        설정 드로어 컴포넌트
        관리자가 JSON 설정 파일을 실시간으로 편집할 수 있는 UI 제공
        생성 페이지의 폼 구성을 동적으로 변경 가능
    --}}
    @livewire('jiny-admin2::settings.create-settings-drawer', [
        'jsonPath' => $settingsPath
    ])
</div>

{{--
    리다이렉트 처리 JavaScript
    템플릿 생성 후 브라우저 히스토리를 대체하면서 목록 페이지로 리다이렉트
    뒤로가기 버튼 사용 시 중복 생성 방지
--}}
<script>
    window.addEventListener('redirect-with-replace', event => {
        window.location.replace(event.detail.url);
    });
</script>
@endsection
