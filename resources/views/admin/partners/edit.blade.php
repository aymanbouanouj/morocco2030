@extends('admin.layouts.app')
@php($pageTitle = 'Edit Partner')
@php($pageDescription = 'Update partner profile and relationship metadata.')
@section('content')
    <form method="POST" action="{{ route('admin.partners.update', $partner) }}" enctype="multipart/form-data">@csrf @method('PUT') @php($submitLabel = 'Save Changes') @include('admin.partners._form')</form>
@endsection
