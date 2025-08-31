<div>
    <h2>Usertype Details</h2>
    
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $data['id'] ?? '' }}</dd>
                
                <dt class="col-sm-3">Title</dt>
                <dd class="col-sm-9">{{ $data['title'] ?? '' }}</dd>
                
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $data['description'] ?? '-' }}</dd>
                
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @if($data['enable'] ?? false)
                        <span class="badge bg-success">Enabled</span>
                    @else
                        <span class="badge bg-secondary">Disabled</span>
                    @endif
                </dd>
                
                <dt class="col-sm-3">Position</dt>
                <dd class="col-sm-9">{{ $data['pos'] ?? 0 }}</dd>
                
                <dt class="col-sm-3">Depth</dt>
                <dd class="col-sm-9">{{ $data['depth'] ?? 0 }}</dd>
                
                <dt class="col-sm-3">Reference</dt>
                <dd class="col-sm-9">{{ $data['ref'] ?? 0 }}</dd>
                
                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $data['created_at_formatted'] ?? $data['created_at'] ?? '-' }}</dd>
                
                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $data['updated_at_formatted'] ?? $data['updated_at'] ?? '-' }}</dd>
            </dl>
        </div>
    </div>
</div>