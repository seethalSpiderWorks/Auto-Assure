<div class="card">
    <div class="card-body">
        <div class="form-group mb-3">
            <label for="name" class="form-label">Type name <span class="text-danger">*</span></label>
            <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $type->name) }}" placeholder="Pre-Purchase Inspection" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted d-block mt-1">English &amp; Arabic in one box: <code>Good quality[ar]نوعية جيدة</code> — or fill the Arabic field below.</small>
        </div>

        {{-- Arabic sits beside its English twin, the same pairing the report screen
             uses for "Client Name in Arabic" / "Overview in Arabic", and the same
             pairing this module already has on sections and questions. --}}
        <div class="form-group mb-3">
            <label for="name_ar" class="form-label">Type name in Arabic — اسم النوع بالعربية</label>
            <input id="name_ar" name="name_ar" dir="rtl" class="form-control @error('name_ar') is-invalid @enderror"
                   value="{{ old('name_ar', $type->name_ar) }}" placeholder="فحص ما قبل الشراء">
            @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $type->description) }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label for="description_ar" class="form-label">Description in Arabic — الوصف بالعربية</label>
            <textarea id="description_ar" name="description_ar" dir="rtl" rows="3" class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', $type->description_ar) }}</textarea>
            @error('description_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

        {{-- Calculated Overall Verdict: score, rating, condition and Recommendations
             worked out from the section weights. Off keeps the manual Overall
             Rating and Recommendation the technician submits. --}}
        <div class="form-group mb-0 border-top pt-3 mt-3">
            <label class="form-label d-block mb-2">Do you want to use the Calculated Overall Verdict?</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="has_calculated_verdict" name="has_calculated_verdict" value="1"
                       @checked(old('has_calculated_verdict', $type->has_calculated_verdict ?? false))>
                <label class="custom-control-label" for="has_calculated_verdict">Yes — calculate the verdict and Recommendations from the section weights</label>
            </div>
        </div>

        {{-- Summary options: the titles the inspection's "Summary — a note per
             area" block asks for, in this order. None set means the inspection
             falls back to the standard areas (Exterior, Interior, Engine, …). --}}
        @php
            $summaryRows = old('summary_options', $type->exists
                ? $type->summaryOptions->map(fn ($o) => ['id' => $o->id, 'name' => $o->name, 'name_ar' => $o->name_ar])->all()
                : []);
        @endphp
        <div class="form-group mb-0 border-top pt-3 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0">Summary options</label>
                <button type="button" class="btn btn-sm btn-soft-primary" id="sumopt-add"><i class="bx bx-plus"></i> Add summary</button>
            </div>
            <small class="text-muted d-block mb-2">
                The titles shown under <strong>Summary</strong> on the inspection — the technician writes a note for each, and every one is printed on the report.
                Leave empty to use the standard areas.
            </small>
            @error('summary_options.*.name')<div class="text-danger font-size-12 mb-2">{{ $message }}</div>@enderror

            <div id="sumopt-list">
                @foreach ($summaryRows as $i => $row)
                    <div class="sumopt-row d-flex align-items-center mb-2" style="gap:.5rem;">
                        <div class="d-flex flex-column">
                            <button type="button" class="btn btn-sm btn-light py-0 sumopt-up" title="Move up"><i class="bx bx-chevron-up"></i></button>
                            <button type="button" class="btn btn-sm btn-light py-0 sumopt-down" title="Move down"><i class="bx bx-chevron-down"></i></button>
                        </div>
                        <input type="hidden" data-f="id" name="summary_options[{{ $i }}][id]" value="{{ $row['id'] ?? '' }}">
                        <input type="text" data-f="name" name="summary_options[{{ $i }}][name]" class="form-control" value="{{ $row['name'] ?? '' }}" placeholder="Title, e.g. Exterior" required maxlength="255">
                        <input type="text" data-f="name_ar" name="summary_options[{{ $i }}][name_ar]" class="form-control" dir="rtl" value="{{ $row['name_ar'] ?? '' }}" placeholder="العنوان بالعربية" maxlength="255">
                        <button type="button" class="btn btn-sm btn-outline-danger sumopt-remove" title="Remove"><i class="bx bx-trash"></i></button>
                    </div>
                @endforeach
            </div>
            <p class="text-muted font-size-12 mb-0 {{ count($summaryRows) ? 'd-none' : '' }}" id="sumopt-empty">No summary options — inspections show the standard areas.</p>

            <template id="sumopt-tpl">
                <div class="sumopt-row d-flex align-items-center mb-2" style="gap:.5rem;">
                    <div class="d-flex flex-column">
                        <button type="button" class="btn btn-sm btn-light py-0 sumopt-up" title="Move up"><i class="bx bx-chevron-up"></i></button>
                        <button type="button" class="btn btn-sm btn-light py-0 sumopt-down" title="Move down"><i class="bx bx-chevron-down"></i></button>
                    </div>
                    <input type="hidden" data-f="id" value="">
                    <input type="text" data-f="name" class="form-control" placeholder="Title, e.g. Exterior" required maxlength="255">
                    <input type="text" data-f="name_ar" class="form-control" dir="rtl" placeholder="العنوان بالعربية" maxlength="255">
                    <button type="button" class="btn btn-sm btn-outline-danger sumopt-remove" title="Remove"><i class="bx bx-trash"></i></button>
                </div>
            </template>
        </div>
    </div>
</div>

<div class="mb-4">
    <button class="btn btn-primary">{{ $type->exists ? 'Save Changes' : 'Create Type' }}</button>
    <a href="{{ $type->exists ? route('templates.show', $type) : route('templates.index') }}" class="btn btn-light">Cancel</a>
</div>

<script>
(function () {
    var list = document.getElementById('sumopt-list');
    if (!list) return;
    var empty = document.getElementById('sumopt-empty');

    // Rows post in on-screen order, which becomes the display order.
    function renumber() {
        list.querySelectorAll('.sumopt-row').forEach(function (row, i) {
            row.querySelectorAll('[data-f]').forEach(function (el) {
                el.name = 'summary_options[' + i + '][' + el.dataset.f + ']';
            });
        });
        empty.classList.toggle('d-none', list.children.length > 0);
    }

    document.getElementById('sumopt-add').addEventListener('click', function () {
        list.appendChild(document.getElementById('sumopt-tpl').content.cloneNode(true));
        renumber();
        list.lastElementChild.querySelector('[data-f="name"]').focus();
    });

    list.addEventListener('click', function (e) {
        var btn = e.target.closest('button');
        if (!btn) return;
        var row = btn.closest('.sumopt-row');
        if (btn.classList.contains('sumopt-remove')) row.remove();
        else if (btn.classList.contains('sumopt-up') && row.previousElementSibling) list.insertBefore(row, row.previousElementSibling);
        else if (btn.classList.contains('sumopt-down') && row.nextElementSibling) list.insertBefore(row.nextElementSibling, row);
        renumber();
    });
})();
</script>
