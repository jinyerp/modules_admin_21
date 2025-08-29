@extends('jiny-admin2::layouts.admin')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-4">
    <div class="sm:flex sm:items-center mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ $title ?? 'Edit Template' }}</h1>
            <p class="mt-2 text-sm text-gray-700">{{ $subtitle ?? '템플릿을 수정합니다.' }}</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <a href="{{ url()->previous() }}" class="block rounded-md bg-gray-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                목록으로
            </a>
        </div>
    </div>

    {{-- 수정 폼 Livewire 컴포넌트 --}}
    @livewire('jiny-admin2::admin-edit', [
        'jsonData' => $jsonData,
        'form' => $form,
        'id' => $id
    ])
</div>

{{-- JavaScript for handling redirect with history replacement --}}
<script>
    window.addEventListener('redirect-with-replace', event => {
        window.location.replace(event.detail.url);
    });
</script>
@endsection
