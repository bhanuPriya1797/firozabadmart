@if ($paginator->hasPages())
<nav aria-label="Page navigation" style="text-align:center;">
  <ul class="pagination" style="display:inline-flex;justify-content:center;gap:10px;flex-wrap:wrap;">
    @if ($paginator->onFirstPage())
      <li class="page-item disabled"><span class="page-link" aria-label="Previous" style="border:1px solid #d0d7de;border-radius:9999px;padding:6px 12px;min-width:36px;display:inline-block;text-align:center;color:#9aa4b2;background:#f7f8fa;">&laquo;</span></li>
    @else
      <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous" style="border:1px solid #d0d7de;border-radius:9999px;padding:6px 12px;min-width:36px;display:inline-block;text-align:center;color:#0b1f3f;background:#fff;">&laquo;</a></li>
    @endif

    @foreach ($elements as $element)
      @if (is_string($element))
        <li class="page-item disabled"><span class="page-link" style="border:1px solid #d0d7de;border-radius:9999px;padding:6px 12px;min-width:36px;display:inline-block;text-align:center;color:#9aa4b2;background:#f7f8fa;">{{ $element }}</span></li>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <li class="page-item active" aria-current="page"><span class="page-link" style="border:1px solid #1e5bd4;border-radius:9999px;padding:6px 12px;min-width:36px;display:inline-block;text-align:center;color:#fff;background:#1e5bd4;">{{ $page }}</span></li>
          @else
            <li class="page-item"><a class="page-link" href="{{ $url }}" style="border:1px solid #d0d7de;border-radius:9999px;padding:6px 12px;min-width:36px;display:inline-block;text-align:center;color:#0b1f3f;background:#fff;">{{ $page }}</a></li>
          @endif
        @endforeach
      @endif
    @endforeach

    @if ($paginator->hasMorePages())
      <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next" style="border:1px solid #d0d7de;border-radius:9999px;padding:6px 12px;min-width:36px;display:inline-block;text-align:center;color:#0b1f3f;background:#fff;">&raquo;</a></li>
    @else
      <li class="page-item disabled"><span class="page-link" aria-label="Next" style="border:1px solid #d0d7de;border-radius:9999px;padding:6px 12px;min-width:36px;display:inline-block;text-align:center;color:#9aa4b2;background:#f7f8fa;">&raquo;</span></li>
    @endif
  </ul>
  <p class="text-center mt-3" style="display:block;">Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results</p>
</nav>
@endif
