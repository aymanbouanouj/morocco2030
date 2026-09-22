@extends('admin.layouts.app')
@php($pageTitle = 'Create Match')
@php($pageDescription = 'Schedule a new fixture and assign core competition details.')
@section('content')
    <form method="POST" action="{{ route('admin.matches.store') }}">@csrf @php($submitLabel = 'Create Match') @include('admin.matches._form')</form>
@endsection
