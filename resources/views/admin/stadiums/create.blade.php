@extends('admin.layouts.app')
@php($pageTitle = 'Create Stadium')
@php($pageDescription = 'Register a venue and connect it to a host city.')
@section('content')
    <form method="POST" action="{{ route('admin.stadiums.store') }}">@csrf @php($submitLabel = 'Create Stadium') @include('admin.stadiums._form')</form>
@endsection
