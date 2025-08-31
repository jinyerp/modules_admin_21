<div>
    <h2>Edit Hello</h2>
    
    <form>
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ $form['title'] ?? '' }}" required>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ $form['description'] ?? '' }}</textarea>
        </div>
        
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="enable" name="enable" @if($form['enable'] ?? false) checked @endif>
            <label class="form-check-label" for="enable">
                Enable
            </label>
        </div>
        
        <div class="mb-3">
            <label for="pos" class="form-label">Position</label>
            <input type="number" class="form-control" id="pos" name="pos" value="{{ $form['pos'] ?? 0 }}">
        </div>
        
        <div class="mb-3">
            <label for="depth" class="form-label">Depth</label>
            <input type="number" class="form-control" id="depth" name="depth" value="{{ $form['depth'] ?? 0 }}">
        </div>
        
        <div class="mb-3">
            <label for="ref" class="form-label">Reference</label>
            <input type="number" class="form-control" id="ref" name="ref" value="{{ $form['ref'] ?? 0 }}">
        </div>
    </form>
</div>