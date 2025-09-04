{{-- 
    템플릿 상세 보기 페이지
    선택한 관리자 템플릿의 상세 정보를 표시합니다.
    읽기 전용 뷰로 템플릿의 모든 필드 정보를 확인할 수 있습니다.
--}}
@extends($jsonData['template']['layout'] ??'jiny-admin::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">
    {{-- 
        페이지 헤더 섹션
        페이지 제목, 설명, 목록으로 돌아가기 버튼을 표시
        상세 보기 모드에서 수정/삭제 버튼도 표시 가능
    --}}
    {{-- jsonData와 jsonPath를 직접 전달하여 컴포넌트에서 처리 --}}
    @livewire('jiny-admin::admin-header-with-settings', [
        'jsonData' => $jsonData,
        'jsonPath' => $jsonPath ?? null,
        'mode' => 'show'
    ])

    {{-- 
        삭제 확인 모달 컴포넌트
        템플릿 삭제 시 확인 다이얼로그를 표시
        상세 보기 페이지에서 직접 삭제 가능
    --}}
    @livewire('jiny-admin::admin-delete', [
        'jsonData' => $jsonData
    ])

    {{-- 
        템플릿 상세 정보 컴포넌트
        JSON 설정에 정의된 필드들을 읽기 전용 형태로 표시
        필드 레이블과 값을 깔끔한 레이아웃으로 렌더링
    --}}
    @livewire('jiny-admin::admin-show', [
        'jsonData' => $jsonData,
        'data' => $data,
        'id' => $id,
        'controllerClass' => $controllerClass ?? null
    ])
    
    {{-- 
        설정 드로어 컴포넌트
        관리자가 JSON 설정 파일을 실시간으로 편집할 수 있는 UI 제공
        상세 보기 페이지의 표시 필드를 동적으로 변경 가능
    --}}
    @livewire('jiny-admin::settings.show-settings-drawer', [
        'jsonPath' => $settingsPath
    ])
</div>
@endsection
