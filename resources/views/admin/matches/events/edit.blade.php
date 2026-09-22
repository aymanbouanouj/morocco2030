@extends('admin.layouts.app')

@php
    $pageTitle = 'Edit Match Event';
    $pageDescription = 'Update timeline data for '.$match->slotLabel('home').' vs '.$match->slotLabel('away').'.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.matches.events.update', [$match, $event]) }}">
        @csrf
        @method('PUT')
        @php($submitLabel = 'Save Changes')
        @include('admin.matches.events._form')
    </form>
@endsection
