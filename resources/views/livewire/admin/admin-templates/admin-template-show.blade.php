{{--
    Admin Template Show Component

    템플릿 상세 정보를 표시하는 Livewire 컴포넌트입니다.

    참고: 헤더와 설정 버튼은 별도 컴포넌트(ShowHeaderWithSettings)로 분리됨
--}}
<div class="p-6">
    <div class="space-y-6">
        {{-- 상태 배지와 정보 헤더 --}}
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Template Information</h2>
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $template->enable ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                {{ $template->enable ? 'Enabled' : 'Disabled' }}
            </span>
        </div>

        <!-- Template Details -->
        <div class="bg-gray-50 rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $template->id }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Title</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $template->title }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $template->description ?: 'No description provided' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $template->created_at->format('Y-m-d H:i:s') }}
                        <span class="text-gray-500">({{ $template->created_at->diffForHumans() }})</span>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $template->updated_at->format('Y-m-d H:i:s') }}
                        <span class="text-gray-500">({{ $template->updated_at->diffForHumans() }})</span>
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between space-x-3 pt-6 border-t">

            <button wire:click="confirmDelete"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
                Delete
            </button>

            {{-- 편집 버튼 --}}
            @if ($template)
                <a href="{{ route('admin2.templates.edit', $template) }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    <span>Edit</span>
                </a>
            @endif



        </div>
    </div>
</div>
