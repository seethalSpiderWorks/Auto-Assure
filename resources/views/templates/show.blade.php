@extends('layouts.myfudapp')
@section('content')

@php
    $mediaBadge = fn($v) => $v === 'mandatory' ? 'badge-soft-danger' : ($v === 'optional' ? 'badge-soft-warning' : 'badge-soft-secondary');
    $stepCount = $type->sections->sum(fn($s) => $s->steps->count());
@endphp

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">{{ $type->name }}</h4>
                        <p class="text-muted mb-0">
                            <span class="badge {{ $type->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span>
                            · {{ $type->sections->count() }} sections · {{ $stepCount }} steps
                        </p>
                    </div>
                    <div class="page-title-right">
                        <a href="{{ route('templates.index') }}" class="btn btn-light btn-sm">Back</a>
                        <a href="{{ route('templates.edit', $type) }}" class="btn btn-primary btn-sm"><i class="bx bx-edit-alt"></i> Edit Type</a>
                    </div>
                </div>
            </div>
        </div>

        @include('templates._flash')

        <div class="row justify-content-center">
            <div class="col-lg-9">

                @foreach ($type->sections as $section)
                    <div class="card">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                            <div id="section-label-{{ $section->id }}">
                                @if($section->group_name)<div class="text-success font-size-11 font-weight-bold">{{ $section->group_name }}@if($section->group_name_ar)<span dir="rtl"> — {{ $section->group_name_ar }}</span>@endif</div>@endif
                                <div class="d-flex align-items-center" style="gap:.5rem;">
                                    <span class="badge badge-soft-primary font-size-13">{{ $section->sequence }}</span>
                                    <h5 class="mb-0">{{ $section->section_name }}</h5>
                                    @if($section->section_name_ar)<span class="text-muted" dir="rtl">— {{ $section->section_name_ar }}</span>@endif
                                </div>
                            </div>

                            {{-- Inline edit form (hidden by default) --}}
                            <form method="POST" action="{{ route('sections.update', $section) }}" class="form-inline flex-grow-1 d-none" id="section-edit-{{ $section->id }}" style="gap:.5rem; flex-wrap:wrap;">
                                @csrf @method('PUT')
                                <input type="number" name="sequence" value="{{ $section->sequence }}" class="form-control form-control-sm" style="width:70px;" title="Order">
                                <input type="text" name="group_name" value="{{ $section->group_name }}" class="form-control form-control-sm" placeholder="Main heading (e.g. 1. Inspection Exterior)">
                                <input type="text" name="group_name_ar" dir="rtl" value="{{ $section->group_name_ar }}" class="form-control form-control-sm" placeholder="العنوان الرئيسي">
                                <input type="text" name="section_name" value="{{ $section->section_name }}" class="form-control form-control-sm" placeholder="Section name" required>
                                <input type="text" name="section_name_ar" dir="rtl" value="{{ $section->section_name_ar }}" class="form-control form-control-sm flex-grow-1" placeholder="الاسم بالعربية">
                                <button class="btn btn-sm btn-success">Save</button>
                                <button type="button" class="btn btn-sm btn-light" onclick="toggleSection({{ $section->id }})">Cancel</button>
                            </form>

                            <div class="d-flex align-items-center" style="gap:.5rem;" id="section-actions-{{ $section->id }}">
                                <a href="{{ route('steps.create', $section) }}" class="btn btn-sm btn-soft-primary"><i class="bx bx-plus"></i> Step</a>
                                <button type="button" class="btn btn-sm btn-soft-secondary" onclick="toggleSection({{ $section->id }})">Edit</button>
                                <form method="POST" action="{{ route('sections.destroy', $section) }}" class="d-inline"
                                      data-confirm="The section and all its steps will be removed." data-confirm-title="Delete this section?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-soft-danger"><i class="bx bx-trash"></i></button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse ($section->steps as $step)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start" style="gap:1rem;">
                                            <div>
                                                <div class="d-flex align-items-center" style="gap:.5rem;">
                                                    <span class="text-muted font-size-12">{{ $section->sequence }}.{{ $step->sequence }}</span>
                                                    <span class="font-weight-medium text-dark">{{ $step->question }}</span>
                                                    @if($step->question_ar)<span class="text-muted" dir="rtl">— {{ $step->question_ar }}</span>@endif
                                                </div>
                                                @if ($step->description)<p class="text-muted font-size-12 mb-1 mt-1">{{ $step->description }}</p>@endif
                                                <div class="mt-2">
                                                    @if ($step->show_rating)<span class="badge badge-soft-info font-size-11">★ Rating</span>@endif
                                                    @if ($step->show_text_answer)<span class="badge badge-soft-secondary font-size-11">Text</span>@endif
                                                    @if ($step->show_multiple_choice)<span class="badge badge-soft-primary font-size-11">Choice: {{ implode(' / ', $step->multiple_choice_options ?? []) }}</span>@endif
                                                    @if ($step->show_remedial_suggestions)<span class="badge badge-soft-purple font-size-11" style="background-color:#eef0fd;color:#6f42c1;">Remedial</span>@endif
                                                    @if ($step->photos !== 'not_required')<span class="badge {{ $mediaBadge($step->photos) }} font-size-11">📷 {{ ucfirst($step->photos) }}</span>@endif
                                                    @if ($step->videos !== 'not_required')<span class="badge {{ $mediaBadge($step->videos) }} font-size-11">🎥 {{ ucfirst($step->videos) }}</span>@endif
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center text-nowrap" style="gap:.5rem;">
                                                <a href="{{ route('steps.edit', $step) }}" class="btn btn-sm btn-soft-secondary">Edit</a>
                                                <form method="POST" action="{{ route('steps.destroy', $step) }}" class="d-inline"
                                                      data-confirm="This step will be removed from the template." data-confirm-title="Delete this step?">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-soft-danger"><i class="bx bx-x"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted">
                                        No steps yet. <a href="{{ route('steps.create', $section) }}">Add the first step</a>.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                @endforeach

                {{-- Add section --}}
                <div class="card border-dashed">
                    <div class="card-body">
                        <h5 class="font-size-15 mb-3">Add Section</h5>
                        <form method="POST" action="{{ route('sections.store', $type) }}" class="form-row">
                            @csrf
                            <div class="col-md-6 form-group mb-2">
                                <label class="form-label font-size-12 text-muted">Main heading (optional)</label>
                                <input type="text" name="group_name" class="form-control" placeholder="e.g. 1. Inspection Exterior">
                            </div>
                            <div class="col-md-6 form-group mb-2">
                                <label class="form-label font-size-12 text-muted">Main heading (Arabic) — العنوان الرئيسي</label>
                                <input type="text" name="group_name_ar" dir="rtl" class="form-control" placeholder="مثال: الفحص الخارجي">
                            </div>
                            <div class="col-md-5 form-group mb-0">
                                <label class="form-label font-size-12 text-muted">Section name</label>
                                <input type="text" name="section_name" class="form-control" placeholder="e.g. Engine &amp; Mechanical" required>
                            </div>
                            <div class="col-md-4 form-group mb-0">
                                <label class="form-label font-size-12 text-muted">Section name (Arabic) — بالعربية</label>
                                <input type="text" name="section_name_ar" dir="rtl" class="form-control" placeholder="مثال: المحرك والميكانيكا">
                            </div>
                            <div class="col-md-3 form-group mb-0 align-self-end">
                                <button class="btn btn-dark btn-block"><i class="bx bx-plus"></i> Add Section</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection

@section('js')
<script>
    function toggleSection(id) {
        ['label', 'actions'].forEach(k => document.getElementById('section-' + k + '-' + id).classList.toggle('d-none'));
        document.getElementById('section-edit-' + id).classList.toggle('d-none');
    }
</script>
@endsection
