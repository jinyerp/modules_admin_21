@extends('jiny-admin2::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">
    <div class="sm:flex sm:items-center mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ $title ?? 'Admin Templates' }}</h1>
            <p class="mt-2 text-sm text-gray-700">{{ $subtitle ?? '템플릿 목록을 관리합니다.' }}</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <a href="{{ route('admin2.templates.create') }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                새 템플릿 추가
            </a>
        </div>
    </div>

    {{-- 검색 --}}
    @livewire('jiny-admin2::admin-search', [
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
</div>
@endsection
