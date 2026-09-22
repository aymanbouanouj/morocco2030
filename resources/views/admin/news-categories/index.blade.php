@extends('admin.layouts.app')

@php
    $pageTitle = 'News Categories';
    $pageDescription = 'Organize editorial content into reusable content categories.';
@endphp

@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.news-categories.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search categories">
                <select name="status">
                    <option value="">All statuses</option>
                    @foreach (['active', 'inactive'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>

            <a class="btn btn-primary" href="{{ route('admin.news-categories.create') }}">Create Category</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Status</th>
                        <th>Children</th>
                        <th>News</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>
                                <strong>{{ $category->name }}</strong><br>
                                <span class="meta">{{ $category->slug }}</span>
                            </td>
                            <td>{{ $category->parent?->name ?: 'Root' }}</td>
                            <td><span class="status-badge">{{ $category->status }}</span></td>
                            <td>{{ $category->children_count }}</td>
                            <td>{{ $category->news_count }}</td>
                            <td class="table-actions">
                                <a class="btn-link" href="{{ route('admin.news-categories.edit', $category) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.news-categories.destroy', $category) }}" onsubmit="return confirm('Archive this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-link" type="submit">Archive</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="meta">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $categories->links() }}
    </section>
@endsection
