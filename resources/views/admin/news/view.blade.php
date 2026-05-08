@component('admin.layouts.main')

@slot('title')
News Detail
@endslot

@slot('slot')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">News Detail</h5>
                    <a href="{{ route($ADMIN_ROUTE_NAME.'.news.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Title:</strong></td>
                                    <td>{{ $news->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Posted By:</strong></td>
                                    <td>{{ optional($news->User)->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Brief:</strong></td>
                                    <td>{{ $news->brief }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Content:</strong></td>
                                    <td>{!! $news->content !!}</td>
                                </tr>
                                <tr>
                                    <td><strong>Featured:</strong></td>
                                    <td>
                                        @if($news->featured)
                                            <span class="badge bg-label-success">Yes</span>
                                        @else
                                            <span class="badge bg-label-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($news->status)
                                            <span class="badge bg-label-primary">Active</span>
                                        @else
                                            <span class="badge bg-label-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Sort Order:</strong></td>
                                    <td>{{ $news->sort_order ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td>{{ $news->blog_date ? \Carbon\Carbon::parse($news->blog_date)->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created At:</strong></td>
                                    <td>{{ $news->created_at ? \Carbon\Carbon::parse($news->created_at)->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated At:</strong></td>
                                    <td>{{ $news->updated_at ? \Carbon\Carbon::parse($news->updated_at)->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            @if($news->image)
                                <div class="text-center">
                                    <h6><strong>Image:</strong></h6>
                                    <img src="{{ asset('storage/' . $news->image) }}" 
                                         alt="News Image" 
                                         class="img-fluid rounded" 
                                         style="max-width: 300px;">
                                </div>
                            @endif
                            
                            @if($news->meta_title || $news->meta_keyword || $news->meta_description)
                                <div class="mt-4">
                                    <h6><strong>SEO Information:</strong></h6>
                                    <table class="table table-sm">
                                        @if($news->meta_title)
                                            <tr>
                                                <td><strong>Meta Title:</strong></td>
                                                <td>{{ $news->meta_title }}</td>
                                            </tr>
                                        @endif
                                        @if($news->meta_keyword)
                                            <tr>
                                                <td><strong>Meta Keywords:</strong></td>
                                                <td>{{ $news->meta_keyword }}</td>
                                            </tr>
                                        @endif
                                        @if($news->meta_description)
                                            <tr>
                                                <td><strong>Meta Description:</strong></td>
                                                <td>{{ $news->meta_description }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endslot

@endcomponent
