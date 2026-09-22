@extends('admin.layouts.app')

@php
    $pageTitle = 'Create News Category';
    $pageDescription = 'Add a category for editorial organization.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.news-categories.store') }}">
        @csrf
        @php($submitLabel = 'Create Category')
        @include('admin.news-categories._form')
    </form>
@endsection
