@extends('registration::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('registration.name') !!}</p>
@endsection
