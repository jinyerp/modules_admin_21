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
        @if(isset($jsonData['edit']['formPath']) && !empty($jsonData['edit']['formPath']))
            @includeIf($jsonData['edit']['formPath'])
        @else
            @include('jiny-admin2::template.components.config-error', [
                'title' => '수정 폼 설정 오류',
                'config' => 'edit.formPath'
            ])
        @endif


        <div class="mt-6 flex justify-between">
            @if($settings['enableDelete'] ?? true)
                <button type="button"
                        wire:click="requestDelete"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    삭제
                </button>
            @else
                <div></div>
            @endif
            <div class="flex space-x-3">
                <button type="button"
                        wire:click="cancel"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    취소
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    저장
                </button>
            </div>
        </div>
    </form>
</div>
