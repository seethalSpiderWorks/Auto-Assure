@extends('layouts.myfudapp')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">New Inspection Type</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Templates</a></li>
                            <li class="breadcrumb-item active">New</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @include('templates._flash')

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="POST" action="{{ route('templates.store') }}">
                    @csrf
                    @include('templates._form')
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
