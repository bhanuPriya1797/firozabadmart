@component('admin.layouts.main')

@slot('title')
    Blog Comments - {{ config('app.name') }}
@endslot

@php
    $routeName = App\Helpers\CustomHelper::getAdminRouteName();
@endphp

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Manage Blog Comments</h4>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="status_filter" class="form-label">Status</label>
                    <select class="form-select" id="status_filter">
                        <option value="">All Status</option>
                        <option value="1">Approved</option>
                        <option value="0">Pending</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="blog_filter" class="form-label">Blog</label>
                    <select class="form-select select2" id="blog_filter" data-placeholder="Select blog">
                        <option value="">All Blogs</option>
                        @foreach($blogs as $b)
                            <option value="{{ $b->id }}">{{ $b->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">            
          <table class="datatables-basic table" id="comments-table">
            <thead>
                <tr>
                    <th>Blog</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Website</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
          </table>
        </div>
    </div>
</div>

@slot('footerBlock')
<script>
$(function () {
    const table = $('#comments-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route($routeName.'.blog-comments.index') }}",
            data: function (d) {
                d.status = $('#status_filter').val();
                d.blog_id = $('#blog_filter').val();
            }
        },
        columns: [
            { data: 'blog_title', name: 'blog_title' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'website', name: 'website' },
            { data: 'comment', name: 'comment' },
            { data: 'status_label', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $('#status_filter, #blog_filter').on('change', function () {
        table.ajax.reload();
    });

    $(document).on('click', '.btn-toggle-status', function(){
        const url = $(this).data('url');
        const id = $(this).data('id');
        const status = $(this).data('status');
        $.post(url, {id, status, _token: '{{ csrf_token() }}'}, function(res){
            if(res.status){
                table.ajax.reload(null, false);
            }
        });
    });

    $(document).on('click', '.btn-delete-comment', function(){
        const url = $(this).data('url');
        if (!confirm('Delete this comment?')) return;
        $.post(url, {_token: '{{ csrf_token() }}'}, function(res){
            if(res.status){
                table.ajax.reload(null, false);
            }
        });
    });
    
    $(document).on('click', '.btn-mark-read', function(){
        const url = $(this).data('url');
        const id = $(this).data('id');
        const read = $(this).data('read');
        $.post(url, {id, read, _token: '{{ csrf_token() }}'}, function(res){
            if(res.status){
                table.ajax.reload(null, false);
            }
        });
    });
});
</script>
@endslot
@endcomponent
