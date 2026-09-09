@extends('layouts.myfudapp')
@section('content')

<style>
    .ds-card { border: 0; border-radius: 14px; box-shadow: 0 4px 18px rgba(16,40,70,.06); margin-bottom: 1rem; }
    .ds-thumb { width: 120px; height: 74px; object-fit: contain; background: #fff; border: 1px solid #e4e8ee; border-radius: 8px; }
    .ds-dot { width: 22px; height: 22px; border-radius: 50%; border: 2px solid #aaa; display: inline-block; vertical-align: middle; }
    .ds-key { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .82rem; }
    .ds-row td { vertical-align: middle; }
    .ds-inline { display: none; }
    .ds-inline.is-open { display: table-row; }
    .ds-inline > td { background: #f7f9fc; box-shadow: inset 3px 0 0 var(--bs-success, #04B084); }
    .ds-row.is-editing > td { background: #f7f9fc; }
    .ds-edit.is-open { background: #04B084; border-color: #04B084; color: #fff; }
    /* The add/edit rows are dense, so labels sit small and quiet above each field. */
    #ds-page .form-label { font-size: .74rem; font-weight: 600; color: #6b7280; margin-bottom: .2rem; }
    #ds-page .form-check-label { font-size: .8rem; color: #5b6472; }
    .ds-swatch { width: 100%; height: 31px; padding: 2px; }
    .ds-group { background: #eef3f8; font-size: .72rem; letter-spacing: .05em; text-transform: uppercase;
        font-weight: 700; color: #37458b; padding: .35rem .75rem !important; }
    .ds-addbar { border-top: 1px dashed #e4e8ee; padding-top: .9rem; margin-top: .25rem; }
    .ds-addbar__title { font-size: .74rem; letter-spacing: .05em; text-transform: uppercase; color: #6b7280; font-weight: 700; margin-bottom: .5rem; }
    @media (min-width: 768px) { .w-md-auto { width: auto !important; } }
</style>

{{-- A failed submit reloads the page with the add form hidden, so reopen
     whichever one produced the errors and let old() refill it. --}}
@php($openAdd = $errors->hasAny(['name', 'key', 'image']) ? 'diagrams' : ($errors->hasAny(['label', 'colour']) ? 'colours' : ''))

<div class="page-content" id="ds-page" data-open-add="{{ $openAdd }}">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Damage Setup</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Templates</a></li>
                            <li class="breadcrumb-item active">Damage Setup</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @include('templates._flash')
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- =========================== DIAGRAMS =========================== --}}
        <div class="card ds-card">
            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bx bx-images text-success"></i> Body Diagrams</h5>
                <div class="d-flex align-items-center" style="gap:.75rem;">
                    <span class="text-muted font-size-12">{{ $diagrams->count() }} total</span>
                    <button type="button" class="btn btn-sm btn-primary ds-add-toggle" data-ds-add="diagrams">
                        <i class="bx bx-plus"></i> Add a diagram
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr>
                            <th style="width:130px;">Image</th><th>Section name</th><th>Key</th>
                            <th style="width:90px;">Order</th><th style="width:90px;">Status</th><th style="width:150px;"></th>
                        </tr></thead>
                        <tbody>
                        @forelse ($diagrams as $d)
                            <tr class="ds-row">
                                <td>
                                    @if ($d->imageExists())
                                        <img src="{{ $d->imageUrl() }}" class="ds-thumb" alt="{{ $d->name }}">
                                    @else
                                        <span class="badge badge-soft-danger">image missing</span>
                                    @endif
                                </td>
                                <td>{{ $d->name }}</td>
                                <td><code class="ds-key">{{ $d->key }}</code></td>
                                <td>{{ $d->sequence }}</td>
                                <td><span class="badge {{ $d->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">{{ $d->is_active ? 'Active' : 'Hidden' }}</span></td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-sm btn-light ds-edit" data-ds-target="diagram-{{ $d->id }}">Edit</button>
                                    <form method="POST" action="{{ route('damage-setup.diagrams.destroy', $d) }}" class="d-inline"
                                          onsubmit="return confirm('Remove {{ $d->name }}? Mark-ups already saved on inspections are kept.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="ds-inline" id="diagram-{{ $d->id }}">
                                <td colspan="6">
                                    <form method="POST" action="{{ route('damage-setup.diagrams.update', $d) }}" enctype="multipart/form-data" class="row g-2 align-items-end">
                                        @csrf @method('PUT')
                                        <div class="col-12 col-md-3">
                                            <label class="form-label">Section name</label>
                                            <input name="name" value="{{ $d->name }}" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-8 col-md-2">
                                            <label class="form-label">Key</label>
                                            <input name="key" value="{{ $d->key }}" class="form-control form-control-sm ds-key" required>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <label class="form-label">Replace image</label>
                                            <input type="file" name="image" accept="image/*" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-4 col-md-1">
                                            <label class="form-label">Order</label>
                                            <input type="number" name="sequence" value="{{ $d->sequence }}" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-6 col-md-1">
                                            <div class="form-check form-switch mb-1">
                                                <input class="form-check-input" type="checkbox" role="switch" id="dg-act-{{ $d->id }}" name="is_active" value="1" @checked($d->is_active)>
                                                <label class="form-check-label" for="dg-act-{{ $d->id }}">Active</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-2 text-md-end">
                                            <button class="btn btn-sm btn-success">Save</button>
                                            <button type="button" class="btn btn-sm btn-link text-muted ds-cancel">Cancel</button>
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted">Changing the key orphans mark-ups already saved on inspections.</small>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted text-center py-3">No diagrams yet — the damage section will be hidden on inspections.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="ds-addbar" id="ds-add-diagrams" hidden>
                <div class="ds-addbar__title">Add a diagram</div>
                <form method="POST" action="{{ route('damage-setup.diagrams.store') }}" enctype="multipart/form-data" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-12 col-md-3">
                        <label class="form-label">Section name</label>
                        <input name="name" value="{{ old('name') }}" class="form-control form-control-sm" placeholder="e.g. Roof" required>
                    </div>
                    <div class="col-8 col-md-2">
                        <label class="form-label">Key</label>
                        <input name="key" value="{{ old('key') }}" class="form-control form-control-sm ds-key" placeholder="roof" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" accept="image/*" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-4 col-md-1">
                        <label class="form-label">Order</label>
                        <input type="number" name="sequence" value="{{ old('sequence', $diagrams->count() + 1) }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-6 col-md-1">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" role="switch" id="dg-new-active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="dg-new-active">Active</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-2 text-md-end">
                        <button class="btn btn-sm btn-primary"><i class="bx bx-plus"></i> Add diagram</button>
                        <button type="button" class="btn btn-sm btn-link text-muted ds-add-cancel">Cancel</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        {{-- ============================ COLOURS ============================ --}}
        <div class="card ds-card">
            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bx bx-palette text-success"></i> Damage Colours</h5>
                <div class="d-flex align-items-center" style="gap:.75rem;">
                    <span class="text-muted font-size-12">{{ $colours->count() }} total</span>
                    <button type="button" class="btn btn-sm btn-primary ds-add-toggle" data-ds-add="colours">
                        <i class="bx bx-plus"></i> Add a colour
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr>
                            <th style="width:70px;">Colour</th><th>Label</th><th>Description</th><th>Hex</th>
                            <th style="width:80px;">Order</th><th style="width:90px;">Status</th><th style="width:150px;"></th>
                        </tr></thead>
                        <tbody>
                        @php($groupTitles = $diagrams->pluck('name', 'id')->all() + ['' => 'All diagrams'])
                        @foreach ($groupTitles as $gid => $gname)
                            @php($group = $colourGroups[$gid] ?? collect())
                            @if ($group->isNotEmpty())
                                <tr><td colspan="7" class="ds-group">{{ $gname }}</td></tr>
                            @endif
                            @foreach ($group as $c)
                            <tr class="ds-row">
                                <td><span class="ds-dot" style="background-color: {{ $c->colour }};"></span></td>
                                <td>{{ $c->label }}</td>
                                <td class="text-muted font-size-12">{{ $c->description ?: '—' }}</td>
                                <td><code class="ds-key">{{ $c->colour }}</code></td>
                                <td>{{ $c->sequence }}</td>
                                <td><span class="badge {{ $c->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">{{ $c->is_active ? 'Active' : 'Hidden' }}</span></td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-sm btn-light ds-edit" data-ds-target="colour-{{ $c->id }}">Edit</button>
                                    <form method="POST" action="{{ route('damage-setup.colours.destroy', $c) }}" class="d-inline"
                                          onsubmit="return confirm('Remove {{ $c->label }}? Dots already drawn in it keep their colour.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="ds-inline" id="colour-{{ $c->id }}">
                                <td colspan="7">
                                    <form method="POST" action="{{ route('damage-setup.colours.update', $c) }}" class="row g-2 align-items-end">
                                        @csrf @method('PUT')
                                        <div class="col-12 col-md-2">
                                            <label class="form-label">Diagram</label>
                                            <select name="damage_diagram_id" class="form-select form-select-sm">
                                                <option value="">All diagrams</option>
                                                @foreach ($diagrams as $dg)
                                                    <option value="{{ $dg->id }}" @selected($c->damage_diagram_id == $dg->id)>{{ $dg->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <label class="form-label">Label</label>
                                            <input name="label" value="{{ $c->label }}" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <label class="form-label">Description</label>
                                            <input name="description" value="{{ $c->description }}" class="form-control form-control-sm" placeholder="e.g. Corrosion present">
                                        </div>
                                        <div class="col-8 col-md-1">
                                            <label class="form-label">Colour</label>
                                            <input type="color" name="colour" value="{{ $c->colour }}" class="form-control form-control-color form-control-sm ds-swatch">
                                        </div>
                                        <div class="col-4 col-md-1">
                                            <label class="form-label">Order</label>
                                            <input type="number" name="sequence" value="{{ $c->sequence }}" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-6 col-md-1">
                                            <div class="form-check form-switch mb-1">
                                                <input class="form-check-input" type="checkbox" role="switch" id="cl-act-{{ $c->id }}" name="is_active" value="1" @checked($c->is_active)>
                                                <label class="form-check-label" for="cl-act-{{ $c->id }}">Active</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-2 text-md-end">
                                            <button class="btn btn-sm btn-success">Save</button>
                                            <button type="button" class="btn btn-sm btn-link text-muted ds-cancel">Cancel</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                        @if ($colours->isEmpty())
                            <tr><td colspan="7" class="text-muted text-center py-3">No colours yet — technicians will have nothing to mark with.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                <div class="ds-addbar" id="ds-add-colours" hidden>
                <div class="ds-addbar__title">Add a colour</div>
                <form method="POST" action="{{ route('damage-setup.colours.store') }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-12 col-md-2">
                        <label class="form-label">Diagram</label>
                        <select name="damage_diagram_id" class="form-select form-select-sm">
                            <option value="">All diagrams</option>
                            @foreach ($diagrams as $dg)
                                <option value="{{ $dg->id }}" @selected(old('damage_diagram_id') == $dg->id)>{{ $dg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label">Label</label>
                        <input name="label" value="{{ old('label') }}" class="form-control form-control-sm" placeholder="e.g. Rust" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Description</label>
                        <input name="description" value="{{ old('description') }}" class="form-control form-control-sm" placeholder="e.g. Corrosion present">
                    </div>
                    <div class="col-8 col-md-1">
                        <label class="form-label">Colour</label>
                        <input type="color" name="colour" value="{{ old('colour', '#8e44ad') }}" class="form-control form-control-color form-control-sm ds-swatch">
                    </div>
                    <div class="col-4 col-md-1">
                        <label class="form-label">Order</label>
                        <input type="number" name="sequence" value="{{ old('sequence', 1) }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-6 col-md-1">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" role="switch" id="cl-new-active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="cl-new-active">Active</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-2 text-md-end">
                        <button class="btn btn-sm btn-primary"><i class="bx bx-plus"></i> Add colour</button>
                        <button type="button" class="btn btn-sm btn-link text-muted ds-add-cancel">Cancel</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // One editor open at a time. The first version just toggled a row, so every
    // Edit clicked left another form open and the table filled up with them.
    (function () {
        function closeAll() {
            document.querySelectorAll('.ds-inline.is-open').forEach(function (row) {
                row.classList.remove('is-open');
                var prev = row.previousElementSibling;
                if (prev) { prev.classList.remove('is-editing'); }
            });
            document.querySelectorAll('.ds-edit.is-open').forEach(function (btn) {
                btn.classList.remove('is-open');
                btn.textContent = 'Edit';
            });
        }

        function closeAdds() {
            document.querySelectorAll('.ds-addbar').forEach(function (bar) { bar.hidden = true; });
            document.querySelectorAll('.ds-add-toggle').forEach(function (b) { b.classList.remove('btn-light'); b.classList.add('btn-primary'); });
        }

        function openAdd(which) {
            var bar = document.getElementById('ds-add-' + which);
            if (! bar) { return; }
            closeAll();                 // an editor and the add form never sit open together
            var wasOpen = ! bar.hidden;
            closeAdds();
            if (wasOpen) { return; }
            bar.hidden = false;
            var first = bar.querySelector('input:not([type=hidden]):not([type=checkbox])');
            if (first) { first.focus(); }
            bar.scrollIntoView({ block: 'nearest' });
        }

        document.addEventListener('click', function (e) {
            var add = e.target.closest('.ds-add-toggle');
            if (add) { openAdd(add.dataset.dsAdd); return; }
            if (e.target.closest('.ds-add-cancel')) { closeAdds(); return; }

            var btn = e.target.closest('.ds-edit');

            if (btn) {
                var row = document.getElementById(btn.dataset.dsTarget);
                var wasOpen = row && row.classList.contains('is-open');
                closeAll();
                closeAdds();
                if (row && ! wasOpen) {
                    row.classList.add('is-open');
                    btn.classList.add('is-open');
                    btn.textContent = 'Close';
                    var prev = row.previousElementSibling;
                    if (prev) { prev.classList.add('is-editing'); }
                    var first = row.querySelector('input:not([type=hidden])');
                    if (first) { first.focus(); }
                }
                return;
            }

            if (e.target.closest('.ds-cancel')) { closeAll(); }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { closeAll(); closeAdds(); }
        });
        var reopen = document.getElementById('ds-page').dataset.openAdd;
        if (reopen) { openAdd(reopen); }
    })();
</script>

@endsection
