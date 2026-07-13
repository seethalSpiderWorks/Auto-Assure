@extends('security::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('security.name') !!}</p>
@endsection
