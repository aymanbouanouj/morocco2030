@extends('admin.layouts.app')

@php
    $pageTitle = 'Edit Lineup Entry';
    $pageDescription = 'Update a lineup assignment for '.$match->slotLabel('home').' vs '.$match->slotLabel('away').'.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.matches.lineups.update', [$match, $lineup]) }}">
        @csrf
        @method('PUT')
        @php($submitLabel = 'Save Changes')
        @include('admin.matches.lineups._form')
    </form>
@endsection
