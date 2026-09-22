@extends('admin.layouts.app')
@php($pageTitle = 'Edit Team')
@php($pageDescription = 'Update team identity, grouping, and competition metadata.')
@section('content')
    <form method="POST" action="{{ route('admin.teams.update', $team) }}">@csrf @method('PUT') @php($submitLabel = 'Save Changes') @include('admin.teams._form')</form>
@endsection
