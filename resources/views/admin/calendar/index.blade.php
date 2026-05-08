@component('admin.layouts.main')
  @slot('title') Calendar - {{ config('app.name') }} @endslot
  @php $ADMIN = \App\Helpers\CustomHelper::getAdminRouteName(); @endphp
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Calendar</h4>
      <a href="{{ route($ADMIN.'.calendar.create') }}" class="btn btn-primary">Add Event</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Start</th>
              <th>End</th>
              <th>Location</th>
              <th>Featured</th>
              <th>Status</th>
              <th>Order</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($items as $i => $row)
              <tr>
                <td>{{ $items->firstItem() + $i }}</td>
                <td>{{ $row->title }}</td>
                <td>{{ $row->start_date ? $row->start_date->format('d M Y') : '-' }}</td>
                <td>{{ $row->end_date ? $row->end_date->format('d M Y') : '-' }}</td>
                <td>{{ $row->location }}</td>
                <td>{!! $row->featured ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                <td>{!! $row->status ? '<span class="badge bg-primary">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                <td>{{ $row->sort_order }}</td>
                <td class="text-end">
                  <a href="{{ route($ADMIN.'.calendar.edit', $row->id) }}" class="btn btn-sm btn-label-primary">Edit</a>
                  <form action="{{ route($ADMIN.'.calendar.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this item?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-label-danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="9" class="text-center">No events found.</td></tr>
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
