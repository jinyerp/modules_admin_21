<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="save">
        @if(isset($jsonData['create']['formPath']) && !empty($jsonData['create']['formPath']))
            @includeIf($jsonData['create']['formPath'])
        @else
            <div class="p-6 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-red-800">생성 폼 설정 오류</h3>
                        <p class="text-sm text-red-600 mt-1">
                            JSON 설정 파일에서 'create.formPath' 값이 누락되었거나 비어있습니다.
                        </p>
                        <p class="text-xs text-red-500 mt-2">
                            Error: Missing or empty 'create.formPath' configuration in JSON settings file.
                        </p>
                    </div>
                </div>
            </div>
        @endif


        <div class="mt-6 flex justify-end space-x-3">
            <button type="button"
                    wire:click="cancel"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                취소
            </button>
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                생성
            </button>
        </div>
    </form>
</div>
