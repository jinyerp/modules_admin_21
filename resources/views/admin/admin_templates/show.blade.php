{{--
    Admin Templates Show Page
    
    템플릿 상세보기 페이지입니다.
    
    포함된 Livewire 컴포넌트:
    1. show-header-with-settings: 헤더와 설정 버튼
    2. admin-template-show: 템플릿 상세 정보
    3. detail-settings-drawer: 상세보기 설정 사이드바
    4. delete-confirmation: 삭제 확인 모달
--}}
@extends('jiny-admin2::layouts.admin')

@section('title', 'Template Details')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    {{-- 템플릿 상세 정보 컴포넌트 --}}
    <div class="bg-white rounded-lg shadow-sm">
        @livewire('jiny-admin2::admin-show', ['controller' => $jsonData['controller'] ?? null, 'id' => $id ?? null])
    </div>
</div>
@endsection