@extends('admin.layouts.app')
@php($pageTitle = 'Create Player')
@php($pageDescription = 'Add a player to an existing team roster.')
@section('content')
    <form method="POST" action="{{ route('admin.players.store') }}">@csrf @php($submitLabel = 'Create Player') @include('admin.players._form')</form>
@endsection
