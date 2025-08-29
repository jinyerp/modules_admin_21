<div>
    <div class="mb-3">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" 
               class="form-control @error('forms.name') is-invalid @enderror" 
               wire:model="forms.name"
               placeholder="Enter full name">
        @error('forms.name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" 
               class="form-control @error('forms.email') is-invalid @enderror" 
               wire:model="forms.email"
               placeholder="Enter email address">
        @error('forms.email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">
            Password 
            @if($mode == 'create')
                <span class="text-danger">*</span>
            @else
                <small class="text-muted">(Leave blank to keep current password)</small>
            @endif
        </label>
        <input type="password" 
               class="form-control @error('forms.password') is-invalid @enderror" 
               wire:model="forms.password"
               placeholder="Enter password">
        @error('forms.password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" 
               class="form-control @error('forms.password_confirmation') is-invalid @enderror" 
               wire:model="forms.password_confirmation"
               placeholder="Confirm password">
        @error('forms.password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Role <span class="text-danger">*</span></label>
        <select class="form-select @error('forms.role') is-invalid @enderror" 
                wire:model="forms.role">
            <option value="">Select Role</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
            <option value="super">Super Admin</option>
        </select>
        @error('forms.role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input type="checkbox" 
                   class="form-check-input" 
                   id="emailVerified"
                   wire:model="forms.email_verified"
                   value="1">
            <label class="form-check-label" for="emailVerified">
                Email Verified
            </label>
        </div>
        <small class="text-muted">Check this if the email address has been verified</small>
    </div>

    <div class="alert alert-info">
        <i class="align-middle" data-feather="info"></i>
        <strong>Note:</strong>
        <ul class="mb-0 mt-2">
            <li><strong>User:</strong> Regular user without admin privileges</li>
            <li><strong>Admin:</strong> Can access admin panel and manage basic functions</li>
            <li><strong>Super Admin:</strong> Full system access with all permissions</li>
        </ul>
    </div>
</div>