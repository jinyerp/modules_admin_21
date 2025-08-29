@extends('jiny-admin2::layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold text-gray-900 dark:text-white">
                {{ $jsonData['title']  }}
            </h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                {{ $jsonData['subtitle']  }}
            </p>
        </div>
        @if($jsonData['create'] ?? true)
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <button type="button"
                    wire:click="$dispatch('openModal', { component: 'admin-user-create' })"
                    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500">
                {{ $jsonData['create']['button_text'] ?? '사용자 추가' }}
            </button>
        </div>
        @endif
    </div>

    @livewire('jiny-admin2::admin-table', ['jsonData' => $jsonData])

</div>
@endsection
