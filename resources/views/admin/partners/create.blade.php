@extends('admin.layouts.app')
@php($pageTitle = 'Create Partner')
@php($pageDescription = 'Register a sponsor or institutional partner.')
@section('content')
    <form method="POST" action="{{ route('admin.partners.store') }}" enctype="multipart/form-data">@csrf @php($submitLabel = 'Create Partner') @include('admin.partners._form')</form>
@endsection
