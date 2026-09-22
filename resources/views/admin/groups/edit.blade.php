@extends('admin.layouts.app')

@php
    $pageTitle = 'Edit Group';
    $pageDescription = 'Update group identity and display ordering.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.groups.update', $group) }}">
        @csrf
        @method('PUT')
        @php($submitLabel = 'Save Changes')
        @include('admin.groups._form')
    </form>
@endsection
