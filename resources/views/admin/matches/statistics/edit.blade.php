@extends('admin.layouts.app')

@php
    $pageTitle = 'Edit Match Statistic';
    $pageDescription = 'Update a recorded metric row for '.$match->slotLabel('home').' vs '.$match->slotLabel('away').'.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.matches.statistics.update', [$match, $statistic]) }}">
        @csrf
        @method('PUT')
        @php($submitLabel = 'Save Changes')
        @include('admin.matches.statistics._form')
    </form>
@endsection
