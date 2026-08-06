@php($mcList = array_values(array_filter(array_map(
    'trim',
    explode(',', old('multiple_choice_options', is_array($step->multiple_choice_options) ? implode(', ', $step->multiple_choice_options) : ''))
))))
{{-- Questions with no options of their own (every newly added one) get the standard set. --}}
@php($mcList = $mcList ?: \App\Models\InspectionStep::CHOICE_OPTIONS)
@php($mc = implode(', ', $mcList))

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

        {{-- Multiple choice is the only answer input a question has, so there is no
             picker: the options are simply shown. Rating, text answer, photos and
             videos are not offered; their stored values ride along hidden. --}}
        <div class="border-top pt-3">
            <div class="form-group mb-0">
                <label class="form-label">Choice options</label>
                <input type="hidden" id="multiple_choice_options" name="multiple_choice_options" value="{{ $mc }}">
                <div class="d-flex flex-wrap" style="gap:.4rem;">
                    @foreach ($mcList as $opt)
                        <span class="badge badge-soft-secondary font-size-13">{{ $opt }}</span>
                    @endforeach
                </div>
                <small class="text-muted font-size-12">Every question is answered with these options.</small>
                @error('multiple_choice_options')<div class="text-danger font-size-12 mt-1">{{ $message }}</div>@enderror
            </div>

            <input type="hidden" name="show_remedial_suggestions" value="{{ (int) old('show_remedial_suggestions', $step->show_remedial_suggestions) }}">
            <input type="hidden" name="photos" value="{{ old('photos', $step->photos ?: \App\Models\InspectionStep::MEDIA_NOT_REQUIRED) }}">
            <input type="hidden" name="videos" value="{{ old('videos', $step->videos ?: \App\Models\InspectionStep::MEDIA_NOT_REQUIRED) }}">
        </div>
    </div>
</div>

<div class="mb-4">
    <button class="btn btn-primary">{{ $step->exists ? 'Save Step' : 'Add Step' }}</button>
    <a href="{{ route('templates.show', $section->inspection_type_id) }}" class="btn btn-light">Cancel</a>
</div>
