@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="pagination">
        <div class="field-inline" style="justify-content: space-between;">
            <div class="meta">
                Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}
            </div>

            <div class="field-inline">
                @if ($paginator->onFirstPage())
                    <span class="btn btn-secondary" style="opacity: .6;">Previous</span>
                @else
                    <a class="btn btn-secondary" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="meta">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="btn btn-primary">{{ $page }}</span>
                            @else
                                <a class="btn btn-secondary" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a class="btn btn-secondary" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
                @else
                    <span class="btn btn-secondary" style="opacity: .6;">Next</span>
                @endif
            </div>
        </div>
    </nav>
@endif
