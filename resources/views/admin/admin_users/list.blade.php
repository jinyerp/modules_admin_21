@extends('jiny-admin::layouts.admin')

@section('content')
<div class="container-fluid p-0">
    {{-- Page Header --}}
    <div class="mb-3">
        <div class="row">
            <div class="col-auto">
                <h1 class="h3 mb-3">{{ $title ?? 'Admin Users Management' }}</h1>
            </div>
            <div class="col-auto ms-auto">
                @if(($actions['create']['enabled'] ?? true))
                <a href="{{ route('admin.users.create') }}" class="btn {{ $actions['create']['class'] ?? 'btn-primary' }}">
                    <i data-feather="{{ $actions['create']['icon'] ?? 'user-plus' }}"></i>
                    {{ $actions['create']['label'] ?? 'Add Admin User' }}
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    @if(isset($statistics))
    <div class="row mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Total Users</h5>
                        </div>
                        <div class="col-auto">
                            <div class="stat text-primary">
                                <i data-feather="users"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">{{ number_format($statistics['total']) }}</h1>
                    <div class="mb-0">
                        <span class="text-muted">All registered users</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Super Admins</h5>
                        </div>
                        <div class="col-auto">
                            <div class="stat text-danger">
                                <i data-feather="shield"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">{{ number_format($statistics['supers']) }}</h1>
                    <div class="mb-0">
                        <span class="text-muted">Super administrators</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Admins</h5>
                        </div>
                        <div class="col-auto">
                            <div class="stat text-warning">
                                <i data-feather="user-check"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">{{ number_format($statistics['admins']) }}</h1>
                    <div class="mb-0">
                        <span class="text-muted">Regular administrators</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Recent Users</h5>
                        </div>
                        <div class="col-auto">
                            <div class="stat text-success">
                                <i data-feather="trending-up"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">{{ number_format($statistics['recent']) }}</h1>
                    <div class="mb-0">
                        <span class="text-muted">Last 30 days</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters and Search --}}
    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.users') }}" class="row g-3">
                {{-- Search --}}
                @if(($search['enabled'] ?? true))
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="{{ $search['placeholder'] ?? 'Search by name or email...' }}"
                               value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i data-feather="search"></i>
                        </button>
                    </div>
                </div>
                @endif

                {{-- Role Filter --}}
                @if(($filters['role']['enabled'] ?? true))
                <div class="col-md-2">
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        @foreach($filters['role']['options'] ?? [] as $option)
                        <option value="{{ $option['value'] }}" {{ request('role') == $option['value'] ? 'selected' : '' }}>
                            {{ $option['label'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Status Filter --}}
                @if(($filters['status']['enabled'] ?? true))
                <div class="col-md-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        @foreach($filters['status']['options'] ?? [] as $option)
                        <option value="{{ $option['value'] }}" {{ request('status') == $option['value'] ? 'selected' : '' }}>
                            {{ $option['label'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Per Page --}}
                @if(($pagination['enabled'] ?? true))
                <div class="col-md-2">
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        @foreach($pagination['perPageOptions'] ?? [10, 20, 50, 100] as $option)
                        <option value="{{ $option }}" {{ request('per_page', 20) == $option ? 'selected' : '' }}>
                            {{ $option }} per page
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Reset Button --}}
                <div class="col-md-2">
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary w-100">
                        <i data-feather="refresh-cw"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            @foreach($columns ?? [] as $key => $column)
                                @if($key !== 'actions')
                                <th style="width: {{ $column['width'] ?? 'auto' }}">
                                    @if($column['sortable'] ?? false)
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort_by' => $key,
                                        'sort_order' => request('sort_by') == $key && request('sort_order') == 'asc' ? 'desc' : 'asc'
                                    ]) }}" class="text-decoration-none text-dark">
                                        {{ $column['label'] }}
                                        @if(request('sort_by') == $key)
                                            @if(request('sort_order') == 'asc')
                                                <i data-feather="chevron-up"></i>
                                            @else
                                                <i data-feather="chevron-down"></i>
                                            @endif
                                        @endif
                                    </a>
                                    @else
                                    {{ $column['label'] }}
                                    @endif
                                </th>
                                @endif
                            @endforeach
                            <th style="width: {{ $columns['actions']['width'] ?? '150px' }}">{{ $columns['actions']['label'] ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" 
                                             alt="{{ $user->name }}" 
                                             class="rounded-circle" 
                                             width="32" 
                                             height="32">
                                    </div>
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->email_verified_at)
                                        <i data-feather="check-circle" class="text-success" style="width: 16px; height: 16px;"></i>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role_type == 'super')
                                    <span class="badge bg-danger">Super Admin</span>
                                @elseif($user->role_type == 'admin')
                                    <span class="badge bg-warning">Admin</span>
                                @else
                                    <span class="badge bg-secondary">User</span>
                                @endif
                            </td>
                            <td>
                                @if($user->deleted_at)
                                    <span class="badge bg-danger">Inactive</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if(($actions['edit']['enabled'] ?? true))
                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                       class="btn {{ $actions['edit']['class'] ?? 'btn-info btn-sm' }}"
                                       title="Edit">
                                        <i data-feather="{{ $actions['edit']['icon'] ?? 'edit' }}"></i>
                                    </a>
                                    @endif
                                    
                                    @if(($actions['delete']['enabled'] ?? true))
                                    <form method="POST" 
                                          action="{{ route('admin.users.destroy', $user->id) }}" 
                                          class="d-inline"
                                          onsubmit="return confirm('{{ $actions['delete']['confirm'] ?? 'Are you sure?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn {{ $actions['delete']['class'] ?? 'btn-danger btn-sm' }}"
                                                title="Delete">
                                            <i data-feather="{{ $actions['delete']['icon'] ?? 'trash' }}"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ count($columns ?? []) + 1 }}" class="text-center py-4">
                                <i data-feather="users" class="mb-2"></i>
                                <p>No admin users found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                </div>
                <div>
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush