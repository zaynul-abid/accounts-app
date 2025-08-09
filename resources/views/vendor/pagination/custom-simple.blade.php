<div class="pagination">
    @if ($paginator->onFirstPage())
        <span class="disabled">Previous</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}">Previous</a>
    @endif

    @for ($i = max(1, $paginator->currentPage() - 2); $i <= min($paginator->lastPage(), $paginator->currentPage() + 2); $i++)
        <a href="{{ $paginator->url($i) }}" class="{{ $paginator->currentPage() == $i ? 'active' : '' }}">{{ $i }}</a>
    @endfor

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}">Next</a>
    @else
        <span class="disabled">Next</span>
    @endif
</div>