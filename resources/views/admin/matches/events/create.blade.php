@extends('admin.layouts.app')

@php
    $pageTitle = 'Add Match Event';
    $pageDescription = 'Record a new event for '.$match->slotLabel('home').' vs '.$match->slotLabel('away').'.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.matches.events.store', $match) }}">
        @csrf
        @php($submitLabel = 'Save Event')
        @include('admin.matches.events._form')
    </form>
@endsection
