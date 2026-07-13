@extends('layouts.myfudapp')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Edit Type — {{ $type->name }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Templates</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('templates.show', $type) }}">{{ $type->name }}</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @include('templates._flash')

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="POST" action="{{ route('templates.update', $type) }}">
                    @csrf
                    @method('PUT')
                    @include('templates._form')
                </form>

                <form method="POST" action="{{ route('templates.destroy', $type) }}"
                      data-confirm="This deletes the type and all its sections &amp; steps."
                      data-confirm-title="Delete this template?">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm"><i class="bx bx-trash"></i> Delete type</button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
