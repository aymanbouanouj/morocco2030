<div class="admin-news-actions__group">
    @can('view', $news)
        <a class="admin-news-action-btn" href="{{ route('admin.news.show', $news) }}">View</a>
    @endcan
    @can('update', $news)
        <a class="admin-news-action-btn" href="{{ route('admin.news.edit', $news) }}">Edit</a>
    @endcan
    @can('submitForReview', $news)
        <form method="POST" action="{{ route('admin.news.submit-review', $news) }}" class="admin-news-action-form">
            @csrf
            <button type="submit" class="admin-news-action-btn">Submit</button>
        </form>
    @endcan
    @can('approve', $news)
        <form method="POST" action="{{ route('admin.news.approve', $news) }}" class="admin-news-action-form">
            @csrf
            <button type="submit" class="admin-news-action-btn">Approve</button>
        </form>
    @endcan
    @can('reject', $news)
        <form method="POST" action="{{ route('admin.news.reject', $news) }}" class="admin-news-action-form">
            @csrf
            <button type="submit" class="admin-news-action-btn admin-news-action-btn--danger">Reject</button>
        </form>
    @endcan
    @can('publish', $news)
        <form method="POST" action="{{ route('admin.news.publish', $news) }}" class="admin-news-action-form">
            @csrf
            <button type="submit" class="admin-news-action-btn admin-news-action-btn--primary">Publish</button>
        </form>
    @endcan
    @can('archive', $news)
        <form method="POST" action="{{ route('admin.news.archive', $news) }}" class="admin-news-action-form" onsubmit="return confirm('Archive this news item?');">
            @csrf
            <button type="submit" class="admin-news-action-btn">Archive</button>
        </form>
    @endcan
    @can('delete', $news)
        <form method="POST" action="{{ route('admin.news.destroy', $news) }}" class="admin-news-action-form" onsubmit="return confirm('Remove this news item?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="admin-news-action-btn admin-news-action-btn--danger">Delete</button>
        </form>
    @endcan
</div>
