<div class="space-y-6">
            {{-- JSON 설정에서 정의된 필드들 또는 기본 필드들 --}}
            @if(isset($jsonData['formSections']) && ($settings['formLayout'] ?? 'vertical') === 'sections')
                {{-- 섹션별로 필드 표시 --}}
                @foreach($jsonData['formSections'] as $sectionKey => $section)
                    @if(!isset($section['readonly']) || !$section['readonly'] || ($settings['includeTimestamps'] ?? false))
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $section['title'] }}</h3>
                            @foreach($section['fields'] as $fieldName)
                                @if(isset($form[$fieldName]))
                                    <div class="mb-4">
                                        <label for="{{ $fieldName }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ ucfirst(str_replace('_', ' ', $fieldName)) }}
                                        </label>
                                        @if(in_array($fieldName, ['description', 'content']) || strlen($form[$fieldName]) > 100)
                                            <textarea id="{{ $fieldName }}"
                                                      wire:model="form.{{ $fieldName }}"
                                                      rows="3"
                                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                      @if(isset($section['readonly']) && $section['readonly']) readonly @endif></textarea>
                                        @elseif($fieldName === 'enable')
                                            <div class="flex items-center">
                                                <input type="checkbox"
                                                       id="{{ $fieldName }}"
                                                       wire:model="form.{{ $fieldName }}"
                                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                       @if(isset($section['readonly']) && $section['readonly']) disabled @endif>
                                                <label for="{{ $fieldName }}" class="ml-2 block text-sm text-gray-900">
                                                    활성화
                                                </label>
                                            </div>
                                        @else
                                            <input type="text"
                                                   id="{{ $fieldName }}"
                                                   wire:model="form.{{ $fieldName }}"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                   @if(isset($section['readonly']) && $section['readonly']) readonly @endif>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
            @elseif(isset($jsonData['edit']['fields']))
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
                @php
                    $excludeFields = ['id'];
                    if (!($settings['includeTimestamps'] ?? false)) {
                        $excludeFields = array_merge($excludeFields, ['created_at', 'updated_at']);
                    }
                @endphp
                @foreach($form as $key => $value)
                    @if(!in_array($key, $excludeFields))
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
