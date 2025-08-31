{{-- 
    생성 폼 필드 컴포넌트
    
    이 파일은 새로운 데이터를 생성하기 위한 폼 필드들을 렌더링합니다.
    Livewire 컴포넌트 뷰(template/livewire/admin-create.blade.php)에서 include되어 사용됩니다.
    
    JSON 설정(AdminTemplates.json)에 따라 다음과 같이 동작:
    1. formSections이 정의되고 formLayout이 'sections'인 경우: 섹션별 레이아웃
    2. create.fields가 정의된 경우: 필드 배열 기반 레이아웃
    3. 둘 다 없는 경우: 기본 템플릿 필드 표시
    
    폼 데이터는 Livewire 컴포넌트의 $form 프로퍼티와 양방향 바인딩됩니다.
--}}

<div class="space-y-12">
    {{-- 옵션 1: 섹션 기반 레이아웃 (formSections 설정이 있는 경우) --}}
    @if(isset($jsonData['formSections']) && ($settings['formLayout'] ?? 'vertical') === 'sections')
        {{-- 각 섹션을 순회하며 필드 그룹 렌더링 --}}
        @foreach($jsonData['formSections'] as $sectionKey => $section)
            {{-- readonly 섹션은 생성 시 제외 --}}
            @if(!isset($section['readonly']) || !$section['readonly'])
                <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3 dark:border-white/10">
                    {{-- 섹션 헤더: 제목과 설명 --}}
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

                    {{-- 섹션 필드들: 반응형 그리드 레이아웃 --}}
                    <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                        @foreach($section['fields'] as $fieldName)
                            @if(isset($form[$fieldName]))
                                @php
                                    // 필드 타입과 이름에 따른 그리드 컬럼 너비 결정
                                    $colSpan = 'sm:col-span-3'; // 기본: 절반 너비
                                    $isTextarea = in_array($fieldName, ['description', 'content', 'about', 'settings']);
                                    
                                    if($isTextarea || in_array($fieldName, ['address', 'street_address'])) {
                                        $colSpan = 'col-span-full'; // 전체 너비
                                    } elseif(in_array($fieldName, ['email', 'username', 'name'])) {
                                        $colSpan = 'sm:col-span-4'; // 2/3 너비
                                    } elseif(in_array($fieldName, ['city', 'state', 'zip', 'postal_code'])) {
                                        $colSpan = 'sm:col-span-2'; // 1/3 너비
                                    }
                                @endphp
                                
                                <div class="{{ $colSpan }}">
                                    {{-- 필드 레이블 --}}
                                    <label for="{{ $fieldName }}" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                        {{ ucfirst(str_replace('_', ' ', $fieldName)) }}
                                        @if(in_array($fieldName, ['name']))
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    
                                    <div class="mt-2">
                                        {{-- 체크박스 필드 --}}
                                        @if($fieldName === 'enable')
                                            <div class="flex gap-3">
                                                <div class="flex h-6 shrink-0 items-center">
                                                    <div class="group grid size-4 grid-cols-1">
                                                        <input 
                                                            type="checkbox"
                                                            id="{{ $fieldName }}"
                                                            wire:model="form.{{ $fieldName }}"
                                                            class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:indeterminate:border-indigo-500 dark:indeterminate:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto"
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
                                        {{-- 텍스트영역 필드 --}}
                                        @elseif($isTextarea)
                                            <textarea 
                                                id="{{ $fieldName }}"
                                                wire:model="form.{{ $fieldName }}"
                                                rows="3"
                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                                                @if($fieldName === 'settings') placeholder='{"key": "value"}' @endif
                                            ></textarea>
                                        {{-- 특수 필드들 --}}
                                        @elseif($fieldName === 'slug')
                                            <input 
                                                type="text"
                                                id="{{ $fieldName }}"
                                                wire:model="form.{{ $fieldName }}"
                                                placeholder="자동 생성됩니다"
                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                                            />
                                        @elseif($fieldName === 'version')
                                            <input 
                                                type="text"
                                                id="{{ $fieldName }}"
                                                wire:model="form.{{ $fieldName }}"
                                                placeholder="1.0.0"
                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                                            />
                                        {{-- 일반 텍스트 필드 --}}
                                        @else
                                            <input 
                                                type="text"
                                                id="{{ $fieldName }}"
                                                {{-- name 필드는 실시간 업데이트 (slug 자동 생성용) --}}
                                                wire:model="{{ $fieldName === 'name' ? 'form.name.live' : 'form.' . $fieldName }}"
                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                                                @if($fieldName === 'name') required @endif
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
        
    {{-- 옵션 2: 필드 배열 기반 레이아웃 (create.fields 설정이 있는 경우) --}}
    @elseif(isset($jsonData['create']['fields']))
        <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3 dark:border-white/10">
            {{-- 섹션 헤더 --}}
            <div>
                <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">
                    새 항목 생성
                </h2>
                <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">
                    필요한 정보를 입력하세요.
                </p>
            </div>

            {{-- 필드 그리드 --}}
            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                @foreach($jsonData['create']['fields'] as $field)
                    @php
                        // 필드 타입과 이름에 따른 그리드 컬럼 너비 결정
                        $colSpan = 'sm:col-span-3';
                        if(in_array($field['type'], ['textarea', 'editor']) || 
                           in_array($field['name'], ['description', 'content', 'address', 'street_address', 'about', 'settings'])) {
                            $colSpan = 'col-span-full';
                        } elseif(in_array($field['name'], ['first_name', 'last_name', 'city', 'state', 'zip', 'postal_code'])) {
                            $colSpan = 'sm:col-span-2';
                        } elseif(in_array($field['name'], ['email', 'username', 'name'])) {
                            $colSpan = 'sm:col-span-4';
                        } elseif($field['name'] === 'country') {
                            $colSpan = 'sm:col-span-3';
                        }
                    @endphp
                    
                    <div class="{{ $colSpan }}">
                        {{-- 필드 레이블 --}}
                        <label for="{{ $field['name'] }}" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                            {{ $field['label'] ?? ucfirst(str_replace('_', ' ', $field['name'])) }}
                            @if($field['required'] ?? false)
                                <span class="text-red-500">*</span>
                            @endif
                        </label>
                        
                        <div class="mt-2">
                            {{-- 필드 타입별 입력 컨트롤 렌더링 --}}
                            
                            {{-- 텍스트 입력 --}}
                            @if($field['type'] === 'text')
                                <input 
                                    type="text"
                                    id="{{ $field['name'] }}"
                                    wire:model="{{ $field['name'] === 'name' ? 'form.name.live' : 'form.' . $field['name'] }}"
                                    @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                                    @if($field['required'] ?? false) required @endif
                                />
                            
                            {{-- 이메일 입력 --}}
                            @elseif($field['type'] === 'email')
                                <input 
                                    type="email"
                                    id="{{ $field['name'] }}"
                                    wire:model="form.{{ $field['name'] }}"
                                    @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                    autocomplete="email"
                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                                    @if($field['required'] ?? false) required @endif
                                />
                            
                            {{-- 텍스트영역 --}}
                            @elseif($field['type'] === 'textarea')
                                <textarea 
                                    id="{{ $field['name'] }}"
                                    wire:model="form.{{ $field['name'] }}"
                                    rows="{{ $field['rows'] ?? 3 }}"
                                    @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                                    @if($field['required'] ?? false) required @endif
                                ></textarea>
                                @if(isset($field['help']))
                                    <p class="mt-3 text-sm/6 text-gray-600 dark:text-gray-400">{{ $field['help'] }}</p>
                                @endif
                            
                            {{-- 셀렉트박스 --}}
                            @elseif($field['type'] === 'select')
                                <div class="grid grid-cols-1">
                                    <select 
                                        id="{{ $field['name'] }}"
                                        wire:model="form.{{ $field['name'] }}"
                                        class="col-start-1 row-start-1 w-full appearance-none rounded-md border border-gray-300 bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:*:bg-gray-800 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                                    >
                                        <option value="">선택하세요</option>
                                        @foreach($field['options'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    {{-- 셀렉트박스 화살표 아이콘 --}}
                                    <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4 dark:text-gray-400">
                                        <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                </div>
                            
                            {{-- 체크박스 --}}
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
                            
                            {{-- 숫자 입력 --}}
                            @elseif($field['type'] === 'number')
                                <input 
                                    type="number"
                                    id="{{ $field['name'] }}"
                                    wire:model="form.{{ $field['name'] }}"
                                    @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                                    @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                                    @if(isset($field['max'])) max="{{ $field['max'] }}" @endif
                                    @if($field['required'] ?? false) required @endif
                                />
                            
                            {{-- 라디오 버튼 --}}
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
        
    {{-- 옵션 3: 기본 템플릿 필드 (JSON 설정이 없는 경우) --}}
    @else
        <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3 dark:border-white/10">
            {{-- 섹션 헤더 --}}
            <div>
                <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">
                    템플릿 정보
                </h2>
                <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">
                    새로운 템플릿을 생성합니다.
                </p>
            </div>

            {{-- 기본 필드들 --}}
            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                {{-- Name 필드 (필수) --}}
                <div class="sm:col-span-4">
                    <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            type="text"
                            id="name"
                            wire:model.live="form.name"
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                            required
                        />
                    </div>
                </div>

                {{-- Slug 필드 (자동 생성) --}}
                <div class="sm:col-span-4">
                    <label for="slug" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                        Slug
                    </label>
                    <div class="mt-2">
                        <input 
                            type="text"
                            id="slug"
                            wire:model="form.slug"
                            placeholder="자동 생성됩니다"
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                        />
                    </div>
                </div>

                {{-- Description 필드 --}}
                <div class="col-span-full">
                    <label for="description" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                        Description
                    </label>
                    <div class="mt-2">
                        <textarea 
                            id="description"
                            wire:model="form.description"
                            rows="3"
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                        ></textarea>
                    </div>
                </div>

                {{-- Category 필드 --}}
                <div class="sm:col-span-3">
                    <label for="category" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                        Category
                    </label>
                    <div class="mt-2">
                        <input 
                            type="text"
                            id="category"
                            wire:model="form.category"
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                        />
                    </div>
                </div>

                {{-- Version 필드 --}}
                <div class="sm:col-span-3">
                    <label for="version" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                        Version
                    </label>
                    <div class="mt-2">
                        <input 
                            type="text"
                            id="version"
                            wire:model="form.version"
                            placeholder="1.0.0"
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                        />
                    </div>
                </div>

                {{-- Author 필드 --}}
                <div class="sm:col-span-3">
                    <label for="author" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                        Author
                    </label>
                    <div class="mt-2">
                        <input 
                            type="text"
                            id="author"
                            wire:model="form.author"
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                        />
                    </div>
                </div>

                {{-- Settings 필드 (JSON) --}}
                <div class="col-span-full">
                    <label for="settings" class="block text-sm/6 font-medium text-gray-900 dark:text-white">
                        Settings
                    </label>
                    <div class="mt-2">
                        <textarea 
                            id="settings"
                            wire:model="form.settings"
                            rows="3"
                            placeholder='{"key": "value"}'
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 sm:text-sm/6 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                        ></textarea>
                    </div>
                </div>

                {{-- Enable 체크박스 --}}
                <div class="col-span-full">
                    <div class="flex gap-3">
                        <div class="flex h-6 shrink-0 items-center">
                            <div class="group grid size-4 grid-cols-1">
                                <input 
                                    type="checkbox"
                                    id="enable"
                                    wire:model="form.enable"
                                    class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:indeterminate:border-indigo-500 dark:indeterminate:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto"
                                />
                                <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25 dark:group-has-disabled:stroke-white/25">
                                    <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                                    <path d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-indeterminate:opacity-100" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-sm/6">
                            <label for="enable" class="font-medium text-gray-900 dark:text-white">
                                템플릿 활성화
                            </label>
                            <p class="text-gray-500 dark:text-gray-400">이 템플릿을 즉시 사용할 수 있도록 활성화합니다.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>