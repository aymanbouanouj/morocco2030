@extends('admin.layouts.app')

@php
    $pageTitle = 'Edit Role';
    $pageDescription = 'Update role identity and permission coverage.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf
        @method('PUT')
        @php($submitLabel = 'Save Changes')
        @include('admin.roles._form')
    </form>
@endsection
