@extends('admin.layouts.app')

@php
    $pageTitle = 'Create Group';
    $pageDescription = 'Add a competition group before assigning teams and group-stage fixtures.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.groups.store') }}">
        @csrf
        @php($submitLabel = 'Create Group')
        @include('admin.groups._form')
    </form>
@endsection
