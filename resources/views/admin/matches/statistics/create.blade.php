@extends('admin.layouts.app')

@php
    $pageTitle = 'Add Match Statistic';
    $pageDescription = 'Record a metric row for '.$match->slotLabel('home').' vs '.$match->slotLabel('away').'.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.matches.statistics.store', $match) }}">
        @csrf
        @php($submitLabel = 'Save Statistic')
        @include('admin.matches.statistics._form')
    </form>
@endsection
