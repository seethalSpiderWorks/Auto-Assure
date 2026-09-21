@extends('layouts.myfudapp')
@section('content')

@php
    $mediaBadge = fn($v) => $v === 'mandatory' ? 'badge-soft-danger' : ($v === 'optional' ? 'badge-soft-warning' : 'badge-soft-secondary');
    $stepCount = $type->sections->sum(fn($s) => $s->steps->count());
    // Section weights make up the Overall Verdict /100 score, so they should total 100.
    $weightTotal = round((float) $type->sections->sum('weight'), 2);
    // Weights only feed the Calculated Overall Verdict, so they are shown and
    // edited only when the template's switch for it is on.
    $showWeight = (bool) $type->has_calculated_verdict;
    $weighted = $showWeight && $type->sections->contains(fn($s) => $s->weight !== null);
    $fmtWeight = fn($w) => rtrim(rtrim(number_format((float) $w, 2), '0'), '.');
@endphp

<style>
    /* Damage-diagram picker inside a section's inline editor. Bootstrap 5 has no
       .custom-control, so these are real tiles driven by a visually hidden box. */
    .dg-head { font-size: .72rem; letter-spacing: .05em; text-transform: uppercase;
        color: #6b7280; font-weight: 700; margin-bottom: .5rem; }
    .dg-grid { display: flex; flex-wrap: wrap; gap: .5rem; }
    .dg-tile { margin: 0; cursor: pointer; }
    .dg-tile input { position: absolute; opacity: 0; width: 0; height: 0; }
    .dg-tile__inner { display: flex; align-items: center; gap: .6rem; padding: .45rem .8rem .45rem .45rem;
        border: 1px solid #e4e8ee; border-radius: 12px; background: #fff; min-width: 190px;
        transition: border-color .12s, box-shadow .12s, background .12s; }
    .dg-tile:hover .dg-tile__inner { border-color: #cfd6df; }
    .dg-tile input:focus-visible + .dg-tile__inner { box-shadow: 0 0 0 3px rgba(4,176,132,.25); }
    .dg-tile input:checked + .dg-tile__inner { border-color: #04B084; background: #f2fbf8; box-shadow: 0 2px 10px rgba(4,176,132,.18); }
    .dg-tile__thumb { width: 54px; height: 34px; flex: 0 0 auto; border-radius: 7px; border: 1px solid #eef1f5;
        background: #fff; display: flex; align-items: center; justify-content: center; overflow: hidden; color: #b6bdc7; }
    .dg-tile__thumb img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .dg-tile__text { display: flex; flex-direction: column; line-height: 1.25; min-width: 0; }
    .dg-tile__name { font-size: .84rem; font-weight: 600; color: #3b4655; }
    .dg-tile__meta { font-size: .7rem; color: #8a94a3; }
    .dg-tile input:checked + .dg-tile__inner .dg-tile__meta { color: #04B084; font-weight: 600; }
    .dg-tile__tick { margin-left: auto; font-size: 17px; color: #cfd6df; }
    .dg-tile input:checked + .dg-tile__inner .dg-tile__tick { color: #04B084; }
</style>

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">{{ $type->name }}@if($type->name_ar)<span class="text-muted font-size-15" dir="rtl"> — {{ $type->name_ar }}</span>@endif</h4>
                        <p class="text-muted mb-0">
                            <span class="badge {{ $type->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span>
                            <span class="badge {{ $type->has_diagnostic_media ? 'badge-soft-info' : 'badge-soft-secondary' }}">Diagnostic Media: {{ $type->has_diagnostic_media ? 'Yes' : 'No' }}</span>
                            · {{ $type->sections->count() }} sections · {{ $stepCount }} steps
                            @if($weighted)
                                · <span class="badge {{ $weightTotal == 100 ? 'badge-soft-success' : 'badge-soft-danger' }}"
                                        title="Section weights for the Overall Verdict score should add up to 100%">Weight total: {{ $fmtWeight($weightTotal) }}%</span>
                            @endif
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
                                    @if($showWeight && $section->weight !== null)<span class="badge badge-soft-warning font-size-12" title="Weight in the Overall Verdict score">{{ $fmtWeight($section->weight) }}%</span>@endif
                                    @foreach ($section->damageDiagrams as $dg)
                                        <span class="badge badge-soft-info font-size-11"><i class="bx bx-palette"></i> {{ $dg->name }}</span>
                                    @endforeach
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
                                @if($showWeight)
                                <div class="input-group input-group-sm" style="width:120px;" title="Weight in the Overall Verdict score">
                                    <input type="number" name="weight" value="{{ $section->weight !== null ? $fmtWeight($section->weight) : '' }}" class="form-control" step="0.01" min="0" max="100" placeholder="Weight">
                                    <span class="input-group-text">%</span>
                                </div>
                                @endif
                                {{-- Damage diagrams drawn inside this step. A diagram belongs to one
                                     section, so ticking it here takes it off whichever section had it.
                                     Tiles rather than checkboxes: the thumbnail is what an admin
                                     recognises, and it says where each one currently sits. --}}
                                <div class="w-100 border-top pt-3 mt-2">
                                    <div class="dg-head">Damage diagrams in this section</div>
                                    @if ($damageDiagrams->isEmpty())
                                        <p class="text-muted font-size-12 mb-0">
                                            None configured yet — add one under <a href="{{ route('damage-setup.index') }}">Damage Setup</a>.
                                        </p>
                                    @else
                                        <div class="dg-grid">
                                            @foreach ($damageDiagrams as $dg)
                                                @php($here = $dg->inspection_section_id === $section->id)
                                                @php($elsewhere = $dg->inspection_section_id && ! $here)
                                                <label class="dg-tile">
                                                    <input type="checkbox" name="damage_diagrams[]" value="{{ $dg->id }}" @checked($here)>
                                                    <span class="dg-tile__inner">
                                                        <span class="dg-tile__thumb">
                                                            @if ($dg->imageExists())
                                                                <img src="{{ $dg->imageUrl() }}" alt="">
                                                            @else
                                                                <i class="bx bx-image-alt"></i>
                                                            @endif
                                                        </span>
                                                        <span class="dg-tile__text">
                                                            <span class="dg-tile__name">{{ $dg->name }}</span>
                                                            <span class="dg-tile__meta">
                                                                @if ($here)
                                                                    In this section
                                                                @elseif ($elsewhere)
                                                                    Currently in {{ optional($dg->section)->section_name }}
                                                                @else
                                                                    Not assigned
                                                                @endif
                                                            </span>
                                                        </span>
                                                        <i class="bx bx-check dg-tile__tick"></i>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <small class="text-muted font-size-11 d-block mt-1">
                                            A diagram sits in one section — ticking it here moves it.
                                        </small>
                                    @endif
                                    {{-- Present even when nothing is ticked, so unticking everything is
                                         a real instruction rather than an absent field. --}}
                                    <input type="hidden" name="damage_diagrams[]" value="">
                                </div>
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
                            <div class="col-md-4 form-group mb-0">
                                <label class="form-label font-size-12 text-muted">Section name</label>
                                <input type="text" name="section_name" class="form-control" placeholder="e.g. Engine &amp; Mechanical  —  or  Engine[ar]المحرك" required>
                            </div>
                            <div class="col-md-{{ $showWeight ? 3 : 5 }} form-group mb-0">
                                <label class="form-label font-size-12 text-muted">Section name (Arabic) — بالعربية</label>
                                <input type="text" name="section_name_ar" dir="rtl" class="form-control" placeholder="مثال: المحرك والميكانيكا">
                            </div>
                            @if($showWeight)
                            <div class="col-md-2 form-group mb-0">
                                <label class="form-label font-size-12 text-muted">Weight (%)</label>
                                <input type="number" name="weight" class="form-control" step="0.01" min="0" max="100" placeholder="e.g. 20">
                            </div>
                            @endif
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
