{{--
    Admin Header With Settings Component
    
    관리자 페이지의 헤더 컴포넌트 - 상태별로 다른 뷰를 렌더링합니다.
    - index: 목록 페이지 헤더 (생성 버튼 포함)
    - show: 상세보기 페이지 헤더 (목록으로 버튼 포함)
    - create: 생성 페이지 헤더 (목록으로 버튼 포함)
    - edit: 수정 페이지 헤더 (목록으로 버튼과 설정 버튼 포함)
--}}
<div>
    @if($mode === 'index')
        @include('jiny-admin2::template.livewire.admin-header-index')
    @elseif($mode === 'show')
        @include('jiny-admin2::template.livewire.admin-header-show')
    @elseif($mode === 'create')
        @include('jiny-admin2::template.livewire.admin-header-create')
    @elseif($mode === 'edit')
        @include('jiny-admin2::template.livewire.admin-header-edit')
    @else
        {{-- 기본 헤더 (mode가 지정되지 않았을 때) --}}
        @include('jiny-admin2::template.livewire.admin-header-index')
    @endif
</div>