<div>
    <h2>Hello Details</h2>
    
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $data['id'] ?? '' }}</dd>
                
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $data['name'] ?? '' }}</dd>
                
                <dt class="col-sm-3">Message</dt>
                <dd class="col-sm-9">{{ $data['message'] ?? '-' }}</dd>
                
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @if($data['is_active'] ?? false)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </dd>
                
                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $data['created_at_formatted'] ?? $data['created_at'] ?? '-' }}</dd>
                
                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $data['updated_at_formatted'] ?? $data['updated_at'] ?? '-' }}</dd>
            </dl>
        </div>
    </div>
</div>