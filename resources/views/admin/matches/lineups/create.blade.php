@extends('admin.layouts.app')

@php
    $pageTitle = 'Add Lineup Entry';
    $pageDescription = 'Assign a player to the matchday lineup for '.$match->slotLabel('home').' vs '.$match->slotLabel('away').'.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.matches.lineups.store', $match) }}">
        @csrf
        @php($submitLabel = 'Save Lineup Entry')
        @include('admin.matches.lineups._form')
    </form>
@endsection
