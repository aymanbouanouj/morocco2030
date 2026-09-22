@extends('admin.layouts.app')
@php($pageTitle = 'Edit Player')
@php($pageDescription = 'Update roster details and player status.')
@section('content')
    <form method="POST" action="{{ route('admin.players.update', $player) }}">@csrf @method('PUT') @php($submitLabel = 'Save Changes') @include('admin.players._form')</form>
@endsection
