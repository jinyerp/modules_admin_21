@extends('jiny-admin2::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">
    <div class="sm:flex sm:items-center mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ $title ?? 'Template Details' }}</h1>
            <p class="mt-2 text-sm text-gray-700">{{ $subtitle ?? '템플릿 상세 정보' }}</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none space-x-2">
            <a href="/admin2/templates/{{ $id }}/edit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                수정
            </a>
            <a href="/admin2/templates" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                목록으로
            </a>
        </div>
    </div>

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
</div>
@endsection