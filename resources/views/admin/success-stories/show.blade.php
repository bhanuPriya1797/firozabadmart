@component('admin.layouts.main')

    @slot('title')
        View Testimonial - {{ config('app.name') }}
    @endslot

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Testimonial Details</h4>
            <div>
                @hasPermission('success_stories.edit')
                <a href="{{ route(CustomHelper::getAdminRouteName() . '.success-stories.edit', $story->encrypted_id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Story
                </a>
                @endHasPermission
                <a href="{{ route(CustomHelper::getAdminRouteName() . '.success-stories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Testimonial Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Client Name:</strong></div>
                            <div class="col-sm-9">{{ $story->title }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Designation:</strong></div>
                            <div class="col-sm-9">{{ $story->brief }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Description:</strong></div>
                            <div class="col-sm-9">
                                <div class="border p-3 rounded bg-light">
                                    {!! $story->description !!}
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Sort Order:</strong></div>
                            <div class="col-sm-9">{{ $story->sort_order }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Status:</strong></div>
                            <div class="col-sm-9">
                                @if($story->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Featured:</strong></div>
                            <div class="col-sm-9">
                                @if($story->featured)
                                    <span class="badge bg-primary">Featured</span>
                                @else
                                    <span class="badge bg-secondary">Regular</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Created:</strong></div>
                            <div class="col-sm-9">{{ $story->created_at->format('M d, Y H:i A') }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Updated:</strong></div>
                            <div class="col-sm-9">{{ $story->updated_at->format('M d, Y H:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Testimonial Image</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($story->image)
                            <img src="{{ $story->image_url }}" alt="{{ $story->title }}" 
                                 class="img-fluid rounded" style="max-height: 300px;">
                            <p class="text-muted small mt-2">Story Image</p>
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <div class="text-center">
                                    <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                    <p class="text-muted">No image uploaded</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @hasPermission('success_stories.edit')
                            <a href="{{ route(CustomHelper::getAdminRouteName() . '.success-stories.edit', $story->encrypted_id) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Testimonial
                            </a>
                            @endHasPermission
                            
                            @hasPermission('success_stories.delete')
                            <button type="button" class="btn btn-danger" onclick="deleteRecord('{{ $story->encrypted_id }}')">
                                <i class="fas fa-trash"></i> Delete Testimonial
                            </button>
                            @endHasPermission
                            
                            <a href="{{ route(CustomHelper::getAdminRouteName() . '.success-stories.index') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-list"></i> All Testimonials
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @slot('scripts')
    <script>
        function deleteRecord(encryptedId) {
            if (confirm('Are you sure you want to delete this success story? This action cannot be undone.')) {
                $.ajax({
                    url: "{{ url(CustomHelper::getAdminRouteName() . '/success-stories') }}/" + encryptedId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.href = "{{ route(CustomHelper::getAdminRouteName() . '.success-stories.index') }}";
                            }, 1500);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr.responseText);
                        toastr.error('Something went wrong!');
                    }
                });
            }
        }
    </script>
    @endslot

@endcomponent
