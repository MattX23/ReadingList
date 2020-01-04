@extends('layouts.app')

@section('content')

@if(!Auth::check())

<login-form
    password-reset-link="{{ route('password.update') }}">
</login-form>

@endif

@endsection
