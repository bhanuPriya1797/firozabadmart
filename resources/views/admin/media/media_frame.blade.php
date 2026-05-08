@php
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $items = isset($medias) ? $medias : collect();
    $files = isset($files) ? $files : [];
    $type = isset($type) ? $type : '';
@endphp

<div class="container-fluid p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Select Media @if(!empty($type)) <small class="text-muted">({{ $type }})</small> @endif</h5>
    </div>

    @if(isset($items) && $items instanceof \Illuminate\Contracts\Pagination\Paginator && $items->count() > 0)
        <div class="row g-3">
            @foreach($items as $media)
                <div class="col-6 col-sm-4 col-md-3">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="flex-grow-1">
                                <div class="text-truncate">{{ $media->caption ?? 'Untitled' }}</div>
                                <small class="text-muted d-block">{{ $media->mime_type ?? '-' }}</small>
                            </div>
                            <div class="mt-2 d-flex justify-content-between">
                                <button type="button" class="btn btn-primary btn-sm" 
                                        onclick="window.parent && window.parent.postMessage({ type: 'media:selected', id: {{ $media->id ?? 0 }} }, '*')">
                                    <i class="ti tabler-check"></i> Select
                                </button>
                                <form action="{{ route($routeName . '.media.files.delete') }}" method="post" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $media->id ?? 0 }}">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete" onclick="return confirm('Delete this media?')">
                                        <i class="ti tabler-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-3">
            {{ $items->links() }}
        </div>
    @elseif(!empty($files))
        <div class="row g-3">
            @foreach($files as $f)
                <div class="col-6 col-sm-4 col-md-3">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="flex-grow-1">
                                <div class="text-truncate">{{ $f['name'] }}</div>
                                <small class="text-muted d-block">{{ $f['mime'] }}</small>
                            </div>
                            <div class="mt-2 d-flex justify-content-between">
                                <button type="button" class="btn btn-primary btn-sm" 
                                        onclick="window.parent && window.parent.postMessage({ type: 'media:selected', path: '{{ $f['path'] }}' }, '*')">
                                    <i class="ti tabler-check"></i> Select
                                </button>
                                <form action="{{ route($routeName . '.media.files.delete') }}" method="post" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="path" value="{{ $f['path'] }}">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete" onclick="return confirm('Delete this file?')">
                                        <i class="ti tabler-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="ti tabler-photo-off icon-lg text-muted mb-3"></i>
            <h6 class="text-muted">No media available</h6>
        </div>
    @endif
</div>
