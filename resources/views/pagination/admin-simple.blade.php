@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="pagination">
        <div class="field-inline" style="justify-content: space-between;">
            <div class="meta">
                Page {{ $paginator->currentPage() }}
            </div>

            <div class="field-inline">
                @if ($paginator->onFirstPage())
                    <span class="btn btn-secondary" style="opacity: .6;">Previous</span>
                @else
                    <a class="btn btn-secondary" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
                @endif

                @if ($paginator->hasMorePages())
                    <a class="btn btn-secondary" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
                @else
                    <span class="btn btn-secondary" style="opacity: .6;">Next</span>
                @endif
            </div>
        </div>
    </nav>
@endif
