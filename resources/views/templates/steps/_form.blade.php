@php($mc = old('multiple_choice_options', is_array($step->multiple_choice_options) ? implode(', ', $step->multiple_choice_options) : ''))

<div class="card">
    <div class="card-body">
        <div class="form-group mb-3">
            <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
            <input id="question" name="question" class="form-control @error('question') is-invalid @enderror"
                   value="{{ old('question', $step->question) }}" placeholder="Overall body condition" required>
            @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group mb-3">
            <label for="question_ar" class="form-label">Question (Arabic) — السؤال بالعربية</label>
            <input id="question_ar" name="question_ar" dir="rtl" class="form-control @error('question_ar') is-invalid @enderror"
                   value="{{ old('question_ar', $step->question_ar) }}" placeholder="نوع البطارية">
            @error('question_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Sub-group heading / helper description fields are hidden (not used) but preserved on submit. --}}
        <input type="hidden" name="description" value="{{ old('description', $step->description) }}">
        <input type="hidden" name="description_ar" value="{{ old('description_ar', $step->description_ar) }}">

        <div class="form-group mb-3">
            <label for="sequence" class="form-label">Display order</label>
            <input id="sequence" name="sequence" type="number" class="form-control" style="max-width:140px;" value="{{ old('sequence', $step->sequence) }}">
        </div>

        <div class="border-top pt-3">
            <p class="font-weight-bold mb-1">Answer inputs to show <span class="text-danger">*</span></p>
            @error('answer_inputs')<div class="text-danger font-size-12 mb-2">{{ $message }}</div>@enderror
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="show_rating" name="show_rating" value="1" @checked(old('show_rating', $step->show_rating))>
                        <label class="custom-control-label" for="show_rating">★ Rating (1–5)</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="show_text_answer" name="show_text_answer" value="1" @checked(old('show_text_answer', $step->show_text_answer))>
                        <label class="custom-control-label" for="show_text_answer">Text answer</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="show_multiple_choice" name="show_multiple_choice" value="1"
                               @checked(old('show_multiple_choice', $step->show_multiple_choice))>
                        <label class="custom-control-label" for="show_multiple_choice">Multiple choice</label>
                    </div>
                </div>
                {{-- Remedial suggestions option is hidden from the UI; the stored value is
                     preserved on save via this hidden input (controller reads it with boolean()). --}}
                <input type="hidden" name="show_remedial_suggestions" value="{{ (int) old('show_remedial_suggestions', $step->show_remedial_suggestions) }}">
            </div>

            @php($mcOn = old('show_multiple_choice', $step->show_multiple_choice))
            @php($mcList = array_values(array_filter(array_map('trim', explode(',', $mc)))))
            {{-- The free-text "Choice options (comma separated)" field is hidden. The
                 question's existing options are shown read-only and preserved on submit. --}}
            <div class="form-group mt-2" id="mc-options" style="display: {{ $mcOn ? '' : 'none' }};">
                <label class="form-label">Choice options</label>
                <input type="hidden" id="multiple_choice_options" name="multiple_choice_options" value="{{ $mc }}">
                <div class="d-flex flex-wrap" style="gap:.4rem;">
                    @forelse ($mcList as $opt)
                        <span class="badge badge-soft-secondary font-size-13">{{ $opt }}</span>
                    @empty
                        <span class="text-muted font-size-12">No options set for this question.</span>
                    @endforelse
                </div>
                @error('multiple_choice_options')<div class="text-danger font-size-12 mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="border-top pt-3 mt-3 row">
            <div class="col-md-6 form-group mb-0">
                <label for="photos" class="form-label">Photos</label>
                <select id="photos" name="photos" class="form-control">
                    @foreach (\App\Models\InspectionStep::MEDIA_OPTIONS as $value => $label)
                        <option value="{{ $value }}" @selected(old('photos', $step->photos) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 form-group mb-0">
                <label for="videos" class="form-label">Videos</label>
                <select id="videos" name="videos" class="form-control">
                    @foreach (\App\Models\InspectionStep::MEDIA_OPTIONS as $value => $label)
                        <option value="{{ $value }}" @selected(old('videos', $step->videos) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <button class="btn btn-primary">{{ $step->exists ? 'Save Step' : 'Add Step' }}</button>
    <a href="{{ route('templates.show', $section->inspection_type_id) }}" class="btn btn-light">Cancel</a>
</div>

<script>
(function () {
    var mc = document.getElementById('show_multiple_choice');
    var mcWrap = document.getElementById('mc-options');

    // Reveal the read-only choice options only while "Multiple choice" is ticked.
    // Options are carried in a hidden field, so nothing is required from the user here.
    function syncMc() {
        mcWrap.style.display = mc.checked ? '' : 'none';
    }
    if (mc) { mc.addEventListener('change', syncMc); syncMc(); }

    // Require at least one answer input before submitting.
    var answerInputs = ['show_rating', 'show_text_answer', 'show_multiple_choice']
        .map(function (id) { return document.getElementById(id); });
    var form = mc ? mc.closest('form') : null;
    if (form) {
        form.addEventListener('submit', function (e) {
            var any = answerInputs.some(function (el) { return el && el.checked; });
            if (!any) {
                e.preventDefault();
                var msg = 'Select at least one answer input: Rating, Text answer, or Multiple choice.';
                if (window.Swal) {
                    Swal.fire({ icon: 'warning', title: 'Answer input required', text: msg, confirmButtonColor: '#f46a6a' });
                } else {
                    alert(msg);
                }
            }
        });
    }
})();
</script>
