@component('admin.layouts.main')

@slot('title')
    Circulars - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Circulars</h4>
        @hasPermission('circulars.create')
        <a href="{{ route($routeName . '.circulars.add') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus me-1"></i> Add Circular
        </a>
        @endHasPermission
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="circularsTable">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Featured</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th style="width: 120px;">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@slot('footerBlock')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const table = $('#circularsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route($routeName . ".circulars.index") }}',
        order: [[3, 'asc']],
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'slug', name: 'slug' },
            { data: 'featured_badge', name: 'featured', orderable: false, searchable: false },
            { data: 'sort_order', name: 'sort_order' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});

function deleteCircular(url){
    if(!confirm('Delete this circular?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = '{{ csrf_token() }}';
    form.appendChild(token);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endslot

@endcomponent
*** End Patch***}"/>
