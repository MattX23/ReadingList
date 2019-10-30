@extends('layouts.app')

@section('content')

@if(!Auth::check())

<login-form></login-form>

@endif

@endsection
