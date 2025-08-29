<div class="p-6">
    <div class="mb-6 flex justify-between items-start">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
            @if($description)
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>
            @endif
        </div>
        
        <div class="flex gap-2">
            @if($createRoute)
                <button type="button"
                        wire:click="createNew"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    새로 만들기
                </button>
            @endif
            
            @if($editRoute)
                <button type="button"
                        wire:click="edit"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    편집
                </button>
            @endif
        </div>
    </div>

    @foreach($sections as $section)
        <div class="mb-6">
            @if(isset($section['title']))
                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                    {{ $section['title'] }}
                </h3>
            @endif
            
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 {{ isset($section['columns']) && $section['columns'] == 2 ? 'sm:grid-cols-2' : '' }}">
                @foreach($section['fields'] as $field)
                    <div class="{{ isset($field['span']) && $field['span'] == 2 ? 'sm:col-span-2' : '' }}">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $field['label'] ?? $field['name'] }}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            @php
                                $fieldName = $field['name'];
                                $value = $data[$fieldName] ?? null;
                            @endphp
                            
                            @if($field['type'] === 'toggle' || $field['type'] === 'boolean')
                                @if(isset($data[$fieldName . '_label']))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $value ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                                        {{ $data[$fieldName . '_label'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $value ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                                        {{ $value ? 'Yes' : 'No' }}
                                    </span>
                                @endif
                            
                            @elseif($field['type'] === 'date' || $field['type'] === 'datetime')
                                @if(isset($data[$fieldName . '_formatted']))
                                    {{ $data[$fieldName . '_formatted'] }}
                                @elseif($value)
                                    {{ \Carbon\Carbon::parse($value)->format($field['format'] ?? 'Y-m-d H:i:s') }}
                                @else
                                    {{ $data[$fieldName . '_display'] ?? '-' }}
                                @endif
                            
                            @elseif($field['type'] === 'badge')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $field['badgeClass'] ?? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' }}">
                                    {{ $value ?? '-' }}
                                </span>
                            
                            @elseif($field['type'] === 'code' || $field['type'] === 'json')
                                <pre class="mt-1 p-2 bg-gray-100 dark:bg-gray-800 rounded text-xs overflow-x-auto">{{ $value ?? '-' }}</pre>
                            
                            @elseif($field['type'] === 'url')
                                @if($value)
                                    <a href="{{ $value }}" target="_blank" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        {{ $value }}
                                    </a>
                                @else
                                    {{ $data[$fieldName . '_display'] ?? '-' }}
                                @endif
                            
                            @elseif($field['type'] === 'email')
                                @if($value)
                                    <a href="mailto:{{ $value }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        {{ $value }}
                                    </a>
                                @else
                                    {{ $data[$fieldName . '_display'] ?? '-' }}
                                @endif
                            
                            @else
                                {{ $data[$fieldName . '_display'] ?? $value ?? '-' }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @endforeach

    <div class="flex justify-between gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
        <div>
            @if($deleteRoute)
                <button type="button"
                        wire:click="delete"
                        wire:confirm="{{ $features['deleteConfirmMessage'] ?? '정말 삭제하시겠습니까?' }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    삭제
                </button>
            @endif
        </div>
        
        <div class="flex gap-3">
            <button type="button"
                    wire:click="backToList"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                목록으로
            </button>
            
            @if($editRoute)
                <button type="button"
                        wire:click="edit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    편집
                </button>
            @endif
        </div>
    </div>
</div>