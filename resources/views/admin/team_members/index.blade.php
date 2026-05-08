@component('admin.layouts.main')
  @slot('title') Team Members - {{ config('app.name') }} @endslot
  @php $ADMIN = \App\Helpers\CustomHelper::getAdminRouteName(); @endphp
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Team Members</h4>
      <a href="{{ route($ADMIN.'.team-members.create') }}" class="btn btn-primary">Add Member</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Designation</th>
              <th>Featured</th>
              <th>Status</th>
              <th>Order</th>
              <th>Image</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($items as $i => $row)
              <tr>
                <td>{{ $items->firstItem() + $i }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->designation }}</td>
                <td>{!! $row->featured ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                <td>{!! $row->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                <td>{{ $row->sort_order }}</td>
                <td><img src="{{ $row->image_url }}" alt="{{ $row->name }}" width="60" height="60" style="object-fit:cover;"></td>
                <td class="text-end">
                  <a href="{{ route($ADMIN.'.team-members.edit', $row->id) }}" class="btn btn-sm btn-label-primary">Edit</a>
                  <form action="{{ route($ADMIN.'.team-members.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this member?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-label-danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center">No team members found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        {{ $items->links() }}
      </div>
    </div>
  </div>
@endcomponent
