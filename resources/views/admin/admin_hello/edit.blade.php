<div>
    <h2>Edit Hello</h2>
    
    <form>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" wire:model="form.name" required>
            @error('form.name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" wire:model="form.message" rows="3"></textarea>
            @error('form.message') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_active" wire:model="form.is_active">
            <label class="form-check-label" for="is_active">
                Active
            </label>
        </div>
    </form>
</div>