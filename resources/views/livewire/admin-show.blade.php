<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Template Information
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Template details and metadata
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin2/templates/{{ $itemId }}/edit" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                    수정
                </a>
                <button wire:click="requestDelete" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700">
                    삭제
                </button>
            </div>
        </div>
    </div>
    <div class="border-t border-gray-200">
        <dl>
            @foreach($data as $key => $value)
                @if($key !== 'settings')
                    <div class="@if($loop->even) bg-gray-50 @else bg-white @endif px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            {{ ucfirst(str_replace('_', ' ', $key)) }}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($key === 'enable')
                                @if($value)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Enabled
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Disabled
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
    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
        <a href="/admin2/templates" 
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
            목록으로
        </a>
    </div>
</div>