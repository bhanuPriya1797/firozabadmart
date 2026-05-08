@component('admin.layouts.main')

@slot('title')
    Services - {{ config('app.name') }}
@endslot

@slot('headerBlock')
@endslot

@php
    $routeName = App\Helpers\CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Services</h4>
        @hasPermission('services.create')
        <a href="{{ route($routeName . '.services.create') }}" class="btn btn-primary">
            <i class="ti tabler-plus me-1"></i> Add Service
        </a>
        @endHasPermission
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Sort</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->slug }}</td>
                                <td>{!! $item->status ? '<span class="badge bg-label-primary">Active</span>' : '<span class="badge bg-label-danger">Inactive</span>' !!}</td>
                                <td>{!! $item->featured ? '<span class="badge bg-label-success">Yes</span>' : '<span class="badge bg-label-secondary">No</span>' !!}</td>
                                <td>{{ $item->sort_order }}</td>
                                <td>
                                    @hasPermission('services.edit')
                                        <a href="{{ route($routeName . '.services.edit', $item->id) }}" class="btn btn-sm btn-info">Edit</a>
                                    @endHasPermission
                                    @hasPermission('services.delete')
                                        <form action="{{ route($routeName . '.services.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    @endHasPermission
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No services found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endcomponent

