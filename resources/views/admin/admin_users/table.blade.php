<div class="card">
    {{-- Search and Filters --}}
    <div class="card-header">
        <div class="row g-3">
            {{-- Search --}}
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" 
                           class="form-control" 
                           placeholder="{{ $actions['search']['placeholder'] ?? 'Search...' }}"
                           wire:model.live.debounce.300ms="search">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="align-middle" data-feather="search"></i>
                    </button>
                </div>
            </div>

            {{-- Role Filter --}}
            @if(isset($actions['filter']['role']))
            <div class="col-md-2">
                <select class="form-select" wire:model.live="filter.role">
                    @foreach($actions['filter']['role'] as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Status Filter --}}
            @if(isset($actions['filter']['status']))
            <div class="col-md-2">
                <select class="form-select" wire:model.live="filter.status">
                    @foreach($actions['filter']['status'] as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Per Page --}}
            <div class="col-md-2">
                <select class="form-select" wire:model.live="paging">
                    <option value="10">10 per page</option>
                    <option value="20">20 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                        </th>
                        @foreach($actions['fields'] as $field => $config)
                        <th style="width: {{ $config['width'] ?? 'auto' }}">
                            {{ $config['label'] }}
                        </th>
                        @endforeach
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($rows && count($rows) > 0)
                        @foreach($rows as $row)
                        <tr>
                            <td>
                                <input type="checkbox" 
                                       value="{{ $row->id }}" 
                                       wire:model.live="selected" 
                                       class="form-check-input">
                            </td>
                            <td>{{ $row->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($row->name) }}&background=random" 
                                         alt="{{ $row->name }}" 
                                         class="rounded-circle me-2" 
                                         width="32" 
                                         height="32">
                                    <strong>{{ $row->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $row->email }}</td>
                            <td>{!! $row->role_badge ?? '' !!}</td>
                            <td>{!! $row->verified_badge ?? '' !!}</td>
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    @if(($actions['edit']['enable'] ?? true))
                                    <button type="button" 
                                            class="btn btn-outline-info"
                                            wire:click="edit({{ $row->id }})"
                                            title="Edit">
                                        <i class="align-middle" data-feather="edit"></i>
                                    </button>
                                    @endif
                                    
                                    @if(($actions['delete']['enable'] ?? true))
                                    <button type="button" 
                                            class="btn btn-outline-danger"
                                            wire:click="delete({{ $row->id }})"
                                            onclick="return confirm('Are you sure?')"
                                            title="Delete">
                                        <i class="align-middle" data-feather="trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="align-middle mb-2" data-feather="users"></i>
                                <p>No admin users found.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rows && $rows->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ $rows->total() }} results
            </div>
            <div>
                {{ $rows->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('refresh-icons', () => {
            setTimeout(() => feather.replace(), 100);
        });
    });
</script>