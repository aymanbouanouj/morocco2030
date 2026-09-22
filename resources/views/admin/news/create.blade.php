@extends('admin.layouts.app')

@php
    $pageTitle = 'Create News';
    $pageDescription = 'Create a new editorial draft.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
        @csrf
        @php($submitLabel = 'Save Draft')
        @include('admin.news._form')
    </form>
@endsection
