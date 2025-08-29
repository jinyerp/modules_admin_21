<!DOCTYPE html>
<html lang="ko" class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    {{-- @vite(['resources/css/app.scss', 'resources/js/app.js']) --}}
    @livewireStyles
    @yield('head')
    @stack('styles')
</head>
<body class="h-full">
    {{-- Mobile Sidebar --}}
    {{-- @include('jiny-admin2::layouts.partials.mobile-sidebar') --}}

    {{-- Desktop Sidebar --}}
    {{-- @include('jiny-admin2::layouts.partials.sidebar') --}}

    {{-- Mobile Header --}}
    {{-- @include('jiny-admin2::layouts.partials.mobile-header') --}}

    {{-- Main Content --}}
    <main class="py-10 lg:pl-72">
        <div class="px-4 sm:px-6 lg:px-8">

            @yield('content')
        </div>
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
