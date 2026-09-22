@extends('admin.layouts.app')

@php
    $pageTitle = 'Edit News Category';
    $pageDescription = 'Update category hierarchy and publishing status.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.news-categories.update', $newsCategory) }}">
        @csrf
        @method('PUT')
        @php($submitLabel = 'Save Changes')
        @include('admin.news-categories._form')
    </form>
@endsection
