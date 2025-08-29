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
        <div class="space-y-6">
            {{-- JSON 설정에서 정의된 필드들 또는 기본 필드들 --}}
            @if(isset($jsonData['edit']['fields']))
                @foreach($jsonData['edit']['fields'] as $field)
                    <div>
                        <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $field['label'] ?? ucfirst(str_replace('_', ' ', $field['name'])) }}
                        </label>
                        
                        @if($field['type'] === 'text')
                            <input type="text" 
                                   id="{{ $field['name'] }}"
                                   wire:model="form.{{ $field['name'] }}" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   @if($field['required'] ?? false) required @endif>
                        @elseif($field['type'] === 'textarea')
                            <textarea id="{{ $field['name'] }}"
                                      wire:model="form.{{ $field['name'] }}"
                                      rows="{{ $field['rows'] ?? 3 }}"
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                      @if($field['required'] ?? false) required @endif></textarea>
                        @elseif($field['type'] === 'select')
                            <select id="{{ $field['name'] }}"
                                    wire:model="form.{{ $field['name'] }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">선택하세요</option>
                                @foreach($field['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif($field['type'] === 'checkbox')
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="{{ $field['name'] }}"
                                       wire:model="form.{{ $field['name'] }}"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="{{ $field['name'] }}" class="ml-2 block text-sm text-gray-900">
                                    {{ $field['description'] ?? '' }}
                                </label>
                            </div>
                        @elseif($field['type'] === 'number')
                            <input type="number" 
                                   id="{{ $field['name'] }}"
                                   wire:model="form.{{ $field['name'] }}" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                                   @if(isset($field['max'])) max="{{ $field['max'] }}" @endif
                                   @if($field['required'] ?? false) required @endif>
                        @endif
                    </div>
                @endforeach
            @else
                {{-- 기본 필드들 (JSON 설정이 없을 경우) --}}
                @foreach($form as $key => $value)
                    @if(!in_array($key, ['id', 'created_at', 'updated_at']))
                        <div>
                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </label>
                            
                            @if($key === 'enable')
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="{{ $key }}"
                                           wire:model="form.{{ $key }}"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="{{ $key }}" class="ml-2 block text-sm text-gray-900">
                                        활성화
                                    </label>
                                </div>
                            @elseif(strlen($value) > 100 || strpos($key, 'description') !== false || strpos($key, 'content') !== false)
                                <textarea id="{{ $key }}"
                                          wire:model="form.{{ $key }}"
                                          rows="3"
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                            @else
                                <input type="text" 
                                       id="{{ $key }}"
                                       wire:model="form.{{ $key }}" 
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @endif
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        <div class="mt-6 flex justify-end space-x-3">
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
    </form>
</div>