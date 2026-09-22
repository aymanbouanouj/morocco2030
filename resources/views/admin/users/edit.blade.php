@extends('admin.layouts.app')

@php
    $pageTitle = $user->isPublic() ? 'Edit Public Audience' : 'Edit Staff Account';
    $pageDescription = $user->isPublic()
        ? 'Update public profile fields only. This account cannot be promoted to staff.'
        : 'Update staff account details and role assignments.';
@endphp

@section('content')
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="admin-users-module">
        @csrf
        @method('PUT')
        @php($submitLabel = 'Save Changes')

        @if ($user->isPublic())
            @include('admin.users._form_public')
        @else
            @include('admin.users._form_staff', ['canAssignRoles' => $canAssignRoles])
        @endif
    </form>
@endsection
