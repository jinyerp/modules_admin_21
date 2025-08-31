<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Module')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- TailwindPlus Elements CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>

    @stack('styles')
    @yield('head')
    @yield('meta')
</head>
<body class="bg-gray-100 dark:bg-gray-900"
    data-page="@yield('script-state')">
    <div class="wrapper">

        @yield('content')

    </div>

    @stack('scripts')
    @yield('scripts')
</body>
</html>


