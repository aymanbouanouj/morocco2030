@extends('admin.layouts.app')
@php($pageTitle = 'Edit City')
@php($pageDescription = 'Update host city data and venue context.')
@section('content')
    <form method="POST" action="{{ route('admin.cities.update', $city) }}">@csrf @method('PUT') @php($submitLabel = 'Save Changes') @include('admin.cities._form')</form>
@endsection
