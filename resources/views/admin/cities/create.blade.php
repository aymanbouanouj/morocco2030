@extends('admin.layouts.app')
@php($pageTitle = 'Create City')
@php($pageDescription = 'Add a host city for venues and match scheduling.')
@section('content')
    <form method="POST" action="{{ route('admin.cities.store') }}">@csrf @php($submitLabel = 'Create City') @include('admin.cities._form')</form>
@endsection
