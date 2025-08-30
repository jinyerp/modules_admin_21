<div class="space-y-6">
    {{-- JSON 설정에서 정의된 필드들 또는 기본 필드들 --}}
    @if (isset($jsonData['create']['fields']))
        @foreach ($jsonData['create']['fields'] as $field)
            <div>
                <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $field['label'] ?? ucfirst(str_replace('_', ' ', $field['name'])) }}
                    @if ($field['required'] ?? false)
                        <span class="text-red-500">*</span>
                    @endif
                </label>

                @if ($field['type'] === 'text')
                    <input type="text" id="{{ $field['name'] }}" wire:model="form.{{ $field['name'] }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        @if ($field['required'] ?? false) required @endif>
                @elseif($field['type'] === 'textarea')
                    <textarea id="{{ $field['name'] }}" wire:model="form.{{ $field['name'] }}" rows="{{ $field['rows'] ?? 3 }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        @if ($field['required'] ?? false) required @endif></textarea>
                @elseif($field['type'] === 'select')
                    <select id="{{ $field['name'] }}" wire:model="form.{{ $field['name'] }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">선택하세요</option>
                        @foreach ($field['options'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                @elseif($field['type'] === 'checkbox')
                    <div class="flex items-center">
                        <input type="checkbox" id="{{ $field['name'] }}" wire:model="form.{{ $field['name'] }}"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="{{ $field['name'] }}" class="ml-2 block text-sm text-gray-900">
                            {{ $field['description'] ?? '' }}
                        </label>
                    </div>
                @elseif($field['type'] === 'number')
                    <input type="number" id="{{ $field['name'] }}" wire:model="form.{{ $field['name'] }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        @if (isset($field['min'])) min="{{ $field['min'] }}" @endif
                        @if (isset($field['max'])) max="{{ $field['max'] }}" @endif
                        @if ($field['required'] ?? false) required @endif>
                @endif
            </div>
        @endforeach
    @else
        {{-- 기본 필드들 (JSON 설정이 없을 경우) --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Name <span class="text-red-500">*</span>
            </label>
            <input type="text" id="name" wire:model.live="form.name"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required>
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                Slug
            </label>
            <input type="text" id="slug" wire:model="form.slug"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="자동 생성됩니다">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                Description
            </label>
            <textarea id="description" wire:model="form.description" rows="3"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
        </div>

        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                Category
            </label>
            <input type="text" id="category" wire:model="form.category"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <div>
            <label for="version" class="block text-sm font-medium text-gray-700 mb-1">
                Version
            </label>
            <input type="text" id="version" wire:model="form.version"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="1.0.0">
        </div>

        <div>
            <label for="author" class="block text-sm font-medium text-gray-700 mb-1">
                Author
            </label>
            <input type="text" id="author" wire:model="form.author"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <div>
            <label for="settings" class="block text-sm font-medium text-gray-700 mb-1">
                Settings
            </label>
            <textarea id="settings" wire:model="form.settings" rows="3"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder='{"key": "value"}'></textarea>
        </div>

        <div class="flex items-center">
            <input type="checkbox" id="enable" wire:model="form.enable"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="enable" class="ml-2 block text-sm text-gray-900">
                Enable (활성화)
            </label>
        </div>

    @endif
</div>
