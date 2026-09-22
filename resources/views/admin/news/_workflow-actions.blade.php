<div class="field-inline">
    @can('submitForReview', $news)
        <form method="POST" action="{{ route('admin.news.submit-review', $news) }}">
            @csrf
            <button class="btn btn-secondary" type="submit">Submit for Review</button>
        </form>
    @endcan

    @can('approve', $news)
        <form method="POST" action="{{ route('admin.news.approve', $news) }}">
            @csrf
            <button class="btn btn-secondary" type="submit">Approve</button>
        </form>
    @endcan

    @can('reject', $news)
        <form method="POST" action="{{ route('admin.news.reject', $news) }}">
            @csrf
            <button class="btn btn-danger" type="submit">Reject</button>
        </form>
    @endcan

    @can('publish', $news)
        <form method="POST" action="{{ route('admin.news.publish', $news) }}">
            @csrf
            <button class="btn btn-primary" type="submit">Publish</button>
        </form>
    @endcan

    @can('archive', $news)
        <form method="POST" action="{{ route('admin.news.archive', $news) }}">
            @csrf
            <button class="btn btn-secondary" type="submit">Archive</button>
        </form>
    @endcan
</div>
