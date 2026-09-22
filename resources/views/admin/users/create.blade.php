@extends('admin.layouts.app')

@php
    $pageTitle = 'Create Staff User';
    $pageDescription = 'Add a new internal staff account. Public audience accounts are created only through public registration.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.users.store') }}" class="admin-users-module">
        @csrf
        @php($submitLabel = 'Create Staff User')
        @php($canAssignRoles = true)
        @include('admin.users._form_staff')
    </form>
@endsection
