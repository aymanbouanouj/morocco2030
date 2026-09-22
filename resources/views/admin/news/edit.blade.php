@extends('admin.layouts.app')

@php
    $pageTitle = 'Edit News';
    $pageDescription = 'Update draft content and editorial metadata.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @php($submitLabel = 'Save Changes')
        @include('admin.news._form')
    </form>

    <section class="panel" style="margin-top: 20px;">
        <h2 class="section-title">Workflow Actions</h2>
        @include('admin.news._workflow-actions', ['news' => $news])
    </section>
@endsection
