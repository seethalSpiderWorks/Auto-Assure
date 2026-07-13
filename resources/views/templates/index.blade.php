@extends('layouts.myfudapp')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Inspection Templates</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Templates</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @include('templates._flash')

        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-3">
                <p class="text-muted mb-0">Each inspection <strong>type</strong> defines its own sequence of sections and steps (questions). Templates are shared across all branches.</p>
                <a href="{{ route('templates.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New Type</a>
            </div>
        </div>

        <div class="row">
            @forelse ($types as $type)
                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="font-size-16 mb-1">
                                    <a href="{{ route('templates.show', $type) }}" class="text-dark">{{ $type->name }}</a>
                                </h5>
                                <span class="badge {{ $type->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }} font-size-12">
                                    {{ $type->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            @if ($type->description)
                                <p class="text-muted mb-3">{{ \Illuminate\Support\Str::limit($type->description, 90) }}</p>
                            @endif
                            <div class="border-top pt-2 mt-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="bx bx-list-ul"></i> {{ $type->sections_count }} sections</span>
                                <a href="{{ route('templates.show', $type) }}" class="btn btn-sm btn-soft-primary">Open <i class="bx bx-right-arrow-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted py-5">
                        No inspection types yet. <a href="{{ route('templates.create') }}">Create one</a> to get started.
                    </div></div>
                </div>
            @endforelse
        </div>

    </div>
</div>

@endsection
