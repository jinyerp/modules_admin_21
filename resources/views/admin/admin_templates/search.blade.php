@php
    $searchableFields = $jsonData['index']['searchable'] ?? ($jsonData['searchable'] ?? []);
@endphp
@if (!empty($searchableFields))
    @foreach ($searchableFields as $field)
        <div>
            <label for="filter_{{ $field }}" class="block text-sm font-medium text-gray-700 mb-1">
                {{ ucfirst(str_replace('_', ' ', $field)) }}
            </label>
            <input type="text" id="filter_{{ $field }}" wire:model="filters.filter_{{ $field }}"
                placeholder="{{ ucfirst($field) }} 검색..."
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
    @endforeach
@endif
