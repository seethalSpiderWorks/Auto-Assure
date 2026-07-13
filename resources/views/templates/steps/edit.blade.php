@extends('layouts.myfudapp')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">Edit Step</h4>
                        <p class="text-muted mb-0">{{ $section->section_name }}</p>
                    </div>
                    <div class="page-title-right">
                        <a href="{{ route('templates.show', $section->inspection_type_id) }}" class="btn btn-light btn-sm">Back</a>
                    </div>
                </div>
            </div>
        </div>

        @include('templates._flash')
        @include('partials._notify')

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="POST" action="{{ route('steps.update', $step) }}">
                    @csrf
                    @method('PUT')
                    @include('templates.steps._form')
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
