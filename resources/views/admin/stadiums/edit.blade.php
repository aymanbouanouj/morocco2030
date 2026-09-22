@extends('admin.layouts.app')
@php($pageTitle = 'Edit Stadium')
@php($pageDescription = 'Update venue assignment and operating details.')
@section('content')
    <form method="POST" action="{{ route('admin.stadiums.update', $stadium) }}">@csrf @method('PUT') @php($submitLabel = 'Save Changes') @include('admin.stadiums._form')</form>
@endsection
