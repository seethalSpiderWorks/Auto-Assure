<div class="card">
    <div class="card-body">
        <div class="form-group mb-3">
            <label for="name" class="form-label">Type name <span class="text-danger">*</span></label>
            <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $type->name) }}" placeholder="Pre-Purchase Inspection" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $type->description) }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-4 form-group mb-3">
                <label for="sequence" class="form-label">Display order</label>
                <input id="sequence" name="sequence" type="number" class="form-control" value="{{ old('sequence', $type->sequence ?? 0) }}">
            </div>
            <div class="col-md-4 form-group mb-3 d-flex align-items-end">
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $type->is_active ?? true))>
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>
        </div>

        {{-- Diagnostic Media is a step-less bucket of extra photos / videos / PDFs.
             Templates that don't need it hide the whole block on the inspection. --}}
        <div class="form-group mb-0 border-top pt-3">
            <label class="form-label d-block mb-2">Do you want to add Diagnostic Media?</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="has_diagnostic_media" name="has_diagnostic_media" value="1"
                       @checked(old('has_diagnostic_media', $type->has_diagnostic_media ?? true))>
                <label class="custom-control-label" for="has_diagnostic_media">Yes — show the Diagnostic Media section in the inspection</label>
            </div>
            <small class="text-muted d-block mt-1">
                Lets the technician attach extra photos, videos and PDF documents that aren't tied to any step.
            </small>
        </div>
    </div>
</div>

<div class="mb-4">
    <button class="btn btn-primary">{{ $type->exists ? 'Save Changes' : 'Create Type' }}</button>
    <a href="{{ $type->exists ? route('templates.show', $type) : route('templates.index') }}" class="btn btn-light">Cancel</a>
</div>
