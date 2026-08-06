@extends('layouts.myfudapp')

@section('css')
<style>
    /* Section name and Back are the two anchors on this screen — the form is
       submitted over and over, so both stay prominent. */
    .step-name-chip{
        display:inline-block; background:#e8f3ff; color:#2f6fb0; border:1px solid #cfe4fb;
        border-radius:6px; padding:.2rem .6rem; font-weight:600; font-size:13px;
    }
    .btn-back-hl{
        background:#5b73e8; border:1px solid #5b73e8; color:#fff; font-weight:600;
        box-shadow:0 2px 6px rgba(91,115,232,.35);
    }
    .btn-back-hl:hover, .btn-back-hl:focus{ background:#4a61d4; border-color:#4a61d4; color:#fff; }
    /* Dark indicator dots — the stock white ones are invisible on a white card. */
    #addedQuestions .carousel-indicators [data-bs-target]{
        width:8px; height:8px; border-radius:50%; background:#adb5bd; border:0; opacity:.5;
    }
    #addedQuestions .carousel-indicators .active{ background:#5b73e8; opacity:1; }
</style>
@endsection

@section('content')

@php
    // Questions already in this section, chunked into carousel slides.
    $addedSteps = $section->steps;
    $slides = $addedSteps->chunk(4);
@endphp

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">New Step</h4>
                        <span class="step-name-chip">{{ $section->section_name }}</span>
                    </div>
                    <div class="page-title-right">
                        <a href="{{ route('templates.show', $section->inspection_type_id) }}" class="btn btn-sm btn-back-hl">
                            <i class="bx bx-arrow-back"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- _flash is itself the notify partial; it must be included once only,
             or SweetAlert2 loads twice and the toast double-fires on each save. --}}
        @include('templates._flash')

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="POST" action="{{ route('steps.store', $section) }}">
                    @csrf
                    @include('templates.steps._form')
                </form>

                {{-- The form returns here after each save, so this carousel is the
                     running record of what has been added to the section. --}}
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 font-size-15">
                                Questions in this section
                                <span class="badge badge-soft-primary font-size-12 ms-1">{{ $addedSteps->count() }}</span>
                            </h5>
                            @if ($slides->count() > 1)
                                <div class="text-nowrap">
                                    <button class="btn btn-sm btn-soft-secondary" type="button" data-bs-target="#addedQuestions" data-bs-slide="prev">
                                        <i class="bx bx-chevron-left"></i>
                                    </button>
                                    <button class="btn btn-sm btn-soft-secondary" type="button" data-bs-target="#addedQuestions" data-bs-slide="next">
                                        <i class="bx bx-chevron-right"></i>
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if ($addedSteps->isEmpty())
                            <p class="text-muted mb-0">No questions yet — the first one you add appears here.</p>
                        @else
                            <div id="addedQuestions" class="carousel slide" data-bs-interval="false">
                                <div class="carousel-inner">
                                    @foreach ($slides as $slide)
                                        <div class="carousel-item @if($loop->first) active @endif">
                                            <ul class="list-group list-group-flush">
                                                @foreach ($slide as $q)
                                                    <li class="list-group-item px-0">
                                                        <div class="d-flex justify-content-between align-items-start" style="gap:1rem;">
                                                            <div>
                                                                <span class="text-muted font-size-12">{{ $section->sequence }}.{{ $q->sequence }}</span>
                                                                <span class="font-weight-medium text-dark">{{ $q->question }}</span>
                                                                @if ($q->question_ar)
                                                                    <span class="text-muted" dir="rtl">— {{ $q->question_ar }}</span>
                                                                @endif
                                                                <div class="mt-1">
                                                                    <span class="badge badge-soft-primary font-size-11">{{ implode(' / ', $q->multiple_choice_options ?? []) }}</span>
                                                                </div>
                                                            </div>
                                                            <a href="{{ route('steps.edit', $q) }}" class="btn btn-sm btn-soft-secondary text-nowrap">Edit</a>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($slides->count() > 1)
                                    <div class="carousel-indicators position-static mt-3 mb-0">
                                        @foreach ($slides as $i => $slide)
                                            <button type="button" data-bs-target="#addedQuestions" data-bs-slide-to="{{ $i }}"
                                                    class="@if($loop->first) active @endif" aria-label="Questions {{ $i + 1 }}"></button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
