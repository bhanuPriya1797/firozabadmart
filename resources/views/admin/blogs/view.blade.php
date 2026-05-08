@component('admin.layouts.main')

@slot('title')
Blog Detail
@endslot

@slot('slot')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Blog Detail</h5>
                    <a href="{{ route($ADMIN_ROUTE_NAME.'.blogs.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Title:</strong></td>
                                    <td>{{ $blog->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Posted By:</strong></td>
                                    <td>{{ optional($blog->User)->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>{{ optional($blog->Category)->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Brief:</strong></td>
                                    <td>{{ $blog->brief }}</td>
                                </tr>
                                @php
                                    $tagSource = !empty($blog->tags) ? $blog->tags : ($blog->meta_keyword ?? '');
                                @endphp
                                @if(!empty($tagSource))
                                <tr>
                                    <td><strong>Tags:</strong></td>
                                    <td>
                                        @foreach(array_filter(array_map('trim', explode(',', $tagSource))) as $tag)
                                            <span class="badge bg-label-primary me-1">{{ $tag }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Content:</strong></td>
                                    <td>{!! $blog->content !!}</td>
                                </tr>
                                <tr>
                                    <td><strong>Featured:</strong></td>
                                    <td>
                                        @if($blog->featured)
                                            <span class="badge bg-label-success">Yes</span>
                                        @else
                                            <span class="badge bg-label-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($blog->status)
                                            <span class="badge bg-label-primary">Active</span>
                                        @else
                                            <span class="badge bg-label-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Sort Order:</strong></td>
                                    <td>{{ $blog->sort_order ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Blog Date:</strong></td>
                                    <td>{{ $blog->blog_date ? \Carbon\Carbon::parse($blog->blog_date)->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created At:</strong></td>
                                    <td>{{ $blog->created_at ? \Carbon\Carbon::parse($blog->created_at)->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated At:</strong></td>
                                    <td>{{ $blog->updated_at ? \Carbon\Carbon::parse($blog->updated_at)->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            @if($blog->image)
                                <div class="text-center">
                                    <h6><strong>Image:</strong></h6>
                                    <img src="{{ asset('storage/' . $blog->image) }}" 
                                         alt="Blog Image" 
                                         class="img-fluid rounded" 
                                         style="max-width: 300px;">
                                </div>
                            @endif
                            
                            @if($blog->meta_title || $blog->meta_keyword || $blog->meta_description)
                                <div class="mt-4">
                                    <h6><strong>SEO Information:</strong></h6>
                                    <table class="table table-sm">
                                        @if($blog->meta_title)
                                            <tr>
                                                <td><strong>Meta Title:</strong></td>
                                                <td>{{ $blog->meta_title }}</td>
                                            </tr>
                                        @endif
                                        @if($blog->meta_keyword)
                                            <tr>
                                                <td><strong>Meta Keywords:</strong></td>
                                                <td>{{ $blog->meta_keyword }}</td>
                                            </tr>
                                        @endif
                                        @if($blog->meta_description)
                                            <tr>
                                                <td><strong>Meta Description:</strong></td>
                                                <td>{{ $blog->meta_description }}</td>
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
