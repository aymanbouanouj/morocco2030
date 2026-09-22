@extends('admin.layouts.app')

@php
    $pageTitle = 'Create Role';
    $pageDescription = 'Define a new administrative role and assign permissions.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        @php($submitLabel = 'Create Role')
        @include('admin.roles._form')
    </form>
@endsection
