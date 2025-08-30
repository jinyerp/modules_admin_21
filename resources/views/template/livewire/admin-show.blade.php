<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="border-t border-gray-200">
        <dl>
            @foreach($data as $key => $value)
                @if($key !== 'settings')
                    <div class="@if($loop->even) bg-gray-50 @else bg-white @endif px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            {{ ucfirst(str_replace('_', ' ', $key)) }}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($key === 'enable' || $key === 'is_default')
                                @if($value)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $key === 'is_default' ? 'Yes' : 'Enabled' }}
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $key === 'is_default' ? 'No' : 'Disabled' }}
                                    </span>
                                @endif
                            @elseif($key === 'created_at' || $key === 'updated_at')
                                {{ $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') : '-' }}
                            @elseif($key === 'description')
                                <div class="prose prose-sm max-w-none">
                                    {{ $value ?? '-' }}
                                </div>
                            @else
                                {{ $value ?? '-' }}
                            @endif
                        </dd>
                    </div>
                @endif
            @endforeach

            @if(isset($data['settings']))
                <div class="@if(count($data) % 2 == 0) bg-white @else bg-gray-50 @endif px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Settings
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($data['settings'])
                            <pre class="bg-gray-100 p-3 rounded text-xs overflow-x-auto">{{ json_encode(json_decode($data['settings']), JSON_PRETTY_PRINT) }}</pre>
                        @else
                            -
                        @endif
                    </dd>
                </div>
            @endif
        </dl>
    </div>

    {{-- 하단 버튼 영역 --}}
    <div class="px-4 py-4 bg-gray-50 flex justify-between items-center">
        {{-- 왼쪽: 삭제 버튼 --}}
        <div>
            @if($jsonData['show']['enableDelete'] ?? true)
                <button wire:click="requestDelete"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    삭제
                </button>
            @endif
        </div>
        
        {{-- 오른쪽: 수정 버튼 --}}
        <div>
            @if($jsonData['show']['enableEdit'] ?? true)
                @php
                    $editRoute = isset($jsonData['route']['name'])
                        ? route($jsonData['route']['name'] . '.edit', $itemId)
                        : "/admin2/templates/{$itemId}/edit";
                @endphp
                <a href="{{ $editRoute }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    수정
                </a>
            @endif
        </div>
    </div>
</div>
