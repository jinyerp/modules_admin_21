<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Templates' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="sm:flex sm:items-center mb-6">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-gray-900">{{ $title ?? 'Admin Templates' }}</h1>
                    <p class="mt-2 text-sm text-gray-700">{{ $subtitle ?? '템플릿 목록을 관리합니다.' }}</p>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <a href="{{ route('admin2.templates.create') }}" 
                       class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        새 템플릿 추가
                    </a>
                </div>
            </div>

            <div class="mt-8">
                @if($controllerClass)
                    @livewire('jiny-admin2::admin-table', ['controller' => $controllerClass])
                @else
                    <p class="text-gray-500">컨트롤러가 설정되지 않았습니다.</p>
                @endif
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>