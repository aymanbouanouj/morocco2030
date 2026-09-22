@extends('admin.layouts.app')
@php($pageTitle = 'Create Team')
@php($pageDescription = 'Add a competition team and assign it to a group.')
@section('content')
    <form method="POST" action="{{ route('admin.teams.store') }}">@csrf @php($submitLabel = 'Create Team') @include('admin.teams._form')</form>
@endsection
