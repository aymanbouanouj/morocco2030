@extends('admin.layouts.app')
@php($pageTitle = 'Edit Match')
@php($pageDescription = 'Update fixture metadata, venue assignment, and scoreline.')
@section('content')
    <form method="POST" action="{{ route('admin.matches.update', $match) }}">@csrf @method('PUT') @php($submitLabel = 'Save Changes') @include('admin.matches._form')</form>
@endsection
