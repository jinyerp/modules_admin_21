{{-- 
    편집 폼 필드 컴포넌트
    
    이 파일은 데이터 편집을 위한 폼 필드들을 렌더링합니다.
    JSON 설정에 따라 섹션별로 구성하거나 단일 폼으로 표시할 수 있습니다.
    
    사용 가능한 설정:
    - formSections: 섹션별로 필드를 그룹화
    - edit.fields: 개별 필드 정의
    - formLayout: 'sections' 또는 'vertical' 레이아웃 선택
--}}

<div class="space-y-12">
    {{-- JSON 설정에서 정의된 필드들 또는 기본 필드들 --}}
    @if(isset($jsonData['formSections']) && ($settings['formLayout'] ?? 'vertical') === 'sections')
        {{-- 섹션별로 필드 표시 --}}
        @foreach($jsonData['formSections'] as $sectionKey => $section)
            @if(!isset($section['readonly']) || !$section['readonly'] || ($settings['includeTimestamps'] ?? false))
                <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3 dark:border-white/10">
                    {{-- 섹션 헤더 --}}
                    <div>
                        <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">
                            {{ $section['title'] }}
                        </h2>
                        @if(isset($section['description']))
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">
                                {{ $section['description'] }}
                            </p>
                        @endif
                    </div>

                    {{-- 섹션 필드들 --}}
                    <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                        @foreach($section['fields'] as $fieldName)
                            @if(isset($form[$fieldName]))
                                @php
                                    $colSpan = 'sm:col-span-3';
                                    $isTextarea = in_array($fieldName, ['description', 'content', 'about']) || 
                                                strlen($form[$fieldName]) > 100;
                                    
                                    if($isTextarea || in_array($fieldName, ['address', 'street_address'])) {
                                        $colSpan = 'col-span-full';
                                    } elseif(in_array($fieldName, ['email', 'username'])) {
                                        $colSpan = 'sm:col-span-4';
                                    } elseif(in_array($fieldName, ['city', 'state', 'zip', 'postal_code'])) {
                                        $colSpan = 'sm:col-span-2';
                                    }
                                @endphp
                                
                                <div class="{{ $colSpan }}">
                                    <label for="{{ $fieldName }}" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                        {{ ucfirst(str_replace('_', ' ', $fieldName)) }}
                                    </label>
                                    <div class="mt-2">
                                        @if($fieldName === 'enable')
                                            <div class="flex gap-3">
                                                <div class="flex h-6 shrink-0 items-center">
                                                    <div class="group grid size-4 grid-cols-1">
                                                        <input 
                                                            type="checkbox"
                                                            id="{{ $fieldName }}"
                                                            wire:model="form.{{ $fieldName }}"
                                                            class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:indeterminate:border-indigo-500 dark:indeterminate:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto"
                                                            @if(isset($section['readonly']) && $section['readonly']) disabled @endif
                                                        />
                                                        <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25 dark:group-has-disabled:stroke-white/25">
                                                            <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                                                            <path d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-indeterminate:opacity-100" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="text-sm/6">
                                                    <label for="{{ $fieldName }}" class="font-medium text-gray-900 dark:text-white">
                                                        활성화
                                                    </label>
                                                    <p class="text-gray-500 dark:text-gray-400">이 항목을 활성화합니다.</p>
                                                </div>
                                            </div>
                                        @elseif($isTextarea)
                                            <textarea 
                                                id="{{ $fieldName }}"
                                                wire:model="form.{{ $fieldName }}"
                                                rows="3"
                                                class="block w-full px-2.5 py-2 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                                @if(isset($section['readonly']) && $section['readonly']) readonly @endif
                                            ></textarea>
                                        @else
                                            <input 
                                                type="text"
                                                id="{{ $fieldName }}"
                                                wire:model="form.{{ $fieldName }}"
                                                class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                                @if(isset($section['readonly']) && $section['readonly']) readonly @endif
                                            />
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
        
    @elseif(isset($jsonData['edit']['fields']))
        {{-- JSON 설정의 fields 배열 사용 --}}
        <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3 dark:border-white/10">
            <div>
                <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">
                    정보 편집
                </h2>
                <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">
                    필요한 정보를 입력하거나 수정하세요.
                </p>
            </div>

            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                @foreach($jsonData['edit']['fields'] as $field)
                    @php
                        $colSpan = 'sm:col-span-3';
                        if(in_array($field['type'], ['textarea', 'editor']) || 
                           in_array($field['name'], ['description', 'content', 'address', 'street_address', 'about'])) {
                            $colSpan = 'col-span-full';
                        } elseif(in_array($field['name'], ['first_name', 'last_name', 'city', 'state', 'zip', 'postal_code'])) {
                            $colSpan = 'sm:col-span-2';
                        } elseif(in_array($field['name'], ['email', 'username'])) {
                            $colSpan = 'sm:col-span-4';
                        } elseif($field['name'] === 'country') {
                            $colSpan = 'sm:col-span-3';
                        }
                    @endphp
                    
                    <div class="{{ $colSpan }}">
                        <label for="{{ $field['name'] }}" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                            {{ $field['label'] ?? ucfirst(str_replace('_', ' ', $field['name'])) }}
                        </label>
                        <div class="mt-2">
                            @if($field['type'] === 'text')
                                <input 
                                    type="text"
                                    id="{{ $field['name'] }}"
                                    wire:model="form.{{ $field['name'] }}"
                                    @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                    class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    @if($field['required'] ?? false) required @endif
                                />
                            @elseif($field['type'] === 'email')
                                <input 
                                    type="email"
                                    id="{{ $field['name'] }}"
                                    wire:model="form.{{ $field['name'] }}"
                                    @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                    autocomplete="email"
                                    class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    @if($field['required'] ?? false) required @endif
                                />
                            @elseif($field['type'] === 'textarea')
                                <textarea 
                                    id="{{ $field['name'] }}"
                                    wire:model="form.{{ $field['name'] }}"
                                    rows="{{ $field['rows'] ?? 3 }}"
                                    @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                    class="block w-full px-2.5 py-2 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    @if($field['required'] ?? false) required @endif
                                ></textarea>
                                @if(isset($field['help']))
                                    <p class="mt-3 text-sm/6 text-gray-600 dark:text-gray-400">{{ $field['help'] }}</p>
                                @endif
                            @elseif($field['type'] === 'select')
                                <div class="grid grid-cols-1">
                                    <select 
                                        id="{{ $field['name'] }}"
                                        wire:model="form.{{ $field['name'] }}"
                                        class="col-start-1 row-start-1 w-full h-8 px-2.5 text-xs border border-gray-200 bg-white rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer"
                                    >
                                        <option value="">선택하세요</option>
                                        @foreach($field['options'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4 dark:text-gray-400">
                                        <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                </div>
                            @elseif($field['type'] === 'checkbox')
                                <div class="flex gap-3">
                                    <div class="flex h-6 shrink-0 items-center">
                                        <div class="group grid size-4 grid-cols-1">
                                            <input 
                                                type="checkbox"
                                                id="{{ $field['name'] }}"
                                                wire:model="form.{{ $field['name'] }}"
                                                class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:indeterminate:border-indigo-500 dark:indeterminate:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto"
                                            />
                                            <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25 dark:group-has-disabled:stroke-white/25">
                                                <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                                                <path d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-indeterminate:opacity-100" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-sm/6">
                                        <label for="{{ $field['name'] }}" class="font-medium text-gray-900 dark:text-white">
                                            {{ $field['label'] ?? ucfirst(str_replace('_', ' ', $field['name'])) }}
                                        </label>
                                        @if(isset($field['description']))
                                            <p class="text-gray-500 dark:text-gray-400">{{ $field['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @elseif($field['type'] === 'number')
                                <input 
                                    type="number"
                                    id="{{ $field['name'] }}"
                                    wire:model="form.{{ $field['name'] }}"
                                    @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                    class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                                    @if(isset($field['max'])) max="{{ $field['max'] }}" @endif
                                    @if($field['required'] ?? false) required @endif
                                />
                            @elseif($field['type'] === 'radio')
                                <div class="mt-6 space-y-6">
                                    @foreach($field['options'] as $value => $label)
                                        <div class="flex items-center gap-x-3">
                                            <input 
                                                id="{{ $field['name'] }}-{{ $value }}"
                                                type="radio"
                                                name="{{ $field['name'] }}"
                                                value="{{ $value }}"
                                                wire:model="form.{{ $field['name'] }}"
                                                class="relative size-4 appearance-none rounded-full border border-gray-300 bg-white before:absolute before:inset-1 before:rounded-full before:bg-white not-checked:before:hidden checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:before:bg-white/20 forced-colors:appearance-auto forced-colors:before:hidden"
                                            />
                                            <label for="{{ $field['name'] }}-{{ $value }}" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
    @else
        {{-- 기본 필드들 (JSON 설정이 없을 경우) --}}
        @php
            $excludeFields = ['id'];
            if (!($settings['includeTimestamps'] ?? false)) {
                $excludeFields = array_merge($excludeFields, ['created_at', 'updated_at']);
            }
        @endphp
        
        <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3 dark:border-white/10">
            <div>
                <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">
                    템플릿 정보
                </h2>
                <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">
                    템플릿에 대한 상세 정보를 입력하세요.
                </p>
            </div>

            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                @foreach($form as $key => $value)
                    @if(!in_array($key, $excludeFields))
                        @php
                            $colSpan = 'sm:col-span-3';
                            $isTextarea = strlen($value) > 100 || 
                                        strpos($key, 'description') !== false || 
                                        strpos($key, 'content') !== false ||
                                        strpos($key, 'about') !== false;
                            
                            if($isTextarea || in_array($key, ['address', 'street_address'])) {
                                $colSpan = 'col-span-full';
                            } elseif(in_array($key, ['name', 'email', 'username'])) {
                                $colSpan = 'sm:col-span-4';
                            } elseif(in_array($key, ['city', 'state', 'province', 'zip', 'postal_code'])) {
                                $colSpan = 'sm:col-span-2';
                            } elseif(in_array($key, ['first_name', 'last_name'])) {
                                $colSpan = 'sm:col-span-3';
                            } elseif($key === 'country') {
                                $colSpan = 'sm:col-span-3';
                            }
                        @endphp
                        
                        <div class="{{ $colSpan }}">
                            <label for="{{ $key }}" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </label>
                            <div class="mt-2">
                                @if($key === 'enable')
                                    <div class="flex gap-3">
                                        <div class="flex h-6 shrink-0 items-center">
                                            <div class="group grid size-4 grid-cols-1">
                                                <input 
                                                    type="checkbox"
                                                    id="{{ $key }}"
                                                    wire:model="form.{{ $key }}"
                                                    class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:indeterminate:border-indigo-500 dark:indeterminate:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto"
                                                />
                                                <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25 dark:group-has-disabled:stroke-white/25">
                                                    <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                                                    <path d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-indeterminate:opacity-100" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="text-sm/6">
                                            <label for="{{ $key }}" class="font-medium text-gray-900 dark:text-white">
                                                활성화
                                            </label>
                                            <p class="text-gray-500 dark:text-gray-400">이 템플릿을 활성화합니다.</p>
                                        </div>
                                    </div>
                                @elseif($isTextarea)
                                    <textarea 
                                        id="{{ $key }}"
                                        wire:model="form.{{ $key }}"
                                        rows="3"
                                        class="block w-full px-2.5 py-2 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    ></textarea>
                                @elseif(strpos($key, 'email') !== false)
                                    <input 
                                        type="email"
                                        id="{{ $key }}"
                                        wire:model="form.{{ $key }}"
                                        autocomplete="email"
                                        class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                @elseif($key === 'country')
                                    <div class="grid grid-cols-1">
                                        <select 
                                            id="{{ $key }}"
                                            wire:model="form.{{ $key }}"
                                            autocomplete="country-name"
                                            class="col-start-1 row-start-1 w-full h-8 px-2.5 text-xs border border-gray-200 bg-white rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer"
                                        >
                                            <option value="">선택하세요</option>
                                            <option value="US">United States</option>
                                            <option value="CA">Canada</option>
                                            <option value="MX">Mexico</option>
                                            <option value="KR">Korea</option>
                                            <option value="JP">Japan</option>
                                            <option value="CN">China</option>
                                        </select>
                                        <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4 dark:text-gray-400">
                                            <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                                        </svg>
                                    </div>
                                @elseif($key === 'city')
                                    <input 
                                        type="text"
                                        id="{{ $key }}"
                                        wire:model="form.{{ $key }}"
                                        autocomplete="address-level2"
                                        class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                @elseif(in_array($key, ['state', 'province', 'region']))
                                    <input 
                                        type="text"
                                        id="{{ $key }}"
                                        wire:model="form.{{ $key }}"
                                        autocomplete="address-level1"
                                        class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                @elseif(in_array($key, ['zip', 'postal_code']))
                                    <input 
                                        type="text"
                                        id="{{ $key }}"
                                        wire:model="form.{{ $key }}"
                                        autocomplete="postal-code"
                                        class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                @elseif($key === 'street_address')
                                    <input 
                                        type="text"
                                        id="{{ $key }}"
                                        wire:model="form.{{ $key }}"
                                        autocomplete="street-address"
                                        class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                @elseif($key === 'first_name')
                                    <input 
                                        type="text"
                                        id="{{ $key }}"
                                        wire:model="form.{{ $key }}"
                                        autocomplete="given-name"
                                        class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                @elseif($key === 'last_name')
                                    <input 
                                        type="text"
                                        id="{{ $key }}"
                                        wire:model="form.{{ $key }}"
                                        autocomplete="family-name"
                                        class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                @else
                                    <input 
                                        type="text"
                                        id="{{ $key }}"
                                        wire:model="form.{{ $key }}"
                                        class="block w-full h-8 px-2.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>