@php $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName(); @endphp
@component('admin.layouts.main')
@slot('title') Dashboard - {{ config('app.name') }} @endslot

<div class="container-xxl flex-grow-1 container-p-y">

  <div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.enquiries.index') }}" class="text-reset text-decoration-none">
      <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted">Enquiries</div>
            <div class="h4 mb-0">{{ $counts['enquiries'] ?? 0 }}</div>
          </div>
          <i class="ti tabler-mail fs-2 text-warning"></i>
        </div>
      </div>
      </a>
    </div>
    <div class="col-xl-3 col-md-6">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.gallery.images.index') }}" class="text-reset text-decoration-none">
      <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted">Gallery Images</div>
            <div class="h4 mb-0">{{ $counts['gallery_images'] ?? 0 }}</div>
          </div>
          <i class="ti tabler-photo fs-2 text-danger"></i>
        </div>
      </div>
      </a>
    </div>
    <div class="col-xl-3 col-md-6">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.news.index') }}" class="text-reset text-decoration-none">
      <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted">News</div>
            <div class="h4 mb-0">{{ $counts['news'] ?? 0 }}</div>
          </div>
          <i class="ti tabler-news fs-2 text-info"></i>
        </div>
      </div>
      </a>
    </div>
    <div class="col-xl-3 col-md-6">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.events.index') }}" class="text-reset text-decoration-none">
      <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted">Events</div>
            <div class="h4 mb-0">{{ $counts['events'] ?? 0 }}</div>
          </div>
          <i class="ti tabler-calendar-event fs-2 text-success"></i>
        </div>
      </div>
      </a>
    </div>
    <div class="col-xl-3 col-md-6">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.newsletter.index') }}" class="text-reset text-decoration-none">
      <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted">Newsletter</div>
            <div class="h4 mb-0">{{ $counts['newsletter'] ?? 0 }}</div>
          </div>
          <i class="ti tabler-mail-opened fs-2 text-primary"></i>
        </div>
      </div>
      </a>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Monthly Activity</h5>
        </div>
        <div class="card-body">
          <div id="adminMonthlyChart" style="min-height:320px;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

@slot('footerBlock')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  (function(){
    var months = @json($chartMonths ?? []);
    var enquiries = @json($chartEnquiries ?? []);
    var options = {
      chart: { type: 'area', height: 320, toolbar: { show: false } },
      stroke: { curve: 'smooth', width: 2 },
      dataLabels: { enabled: false },
      series: [
        { name: 'Enquiries', data: enquiries }
      ],
      xaxis: { categories: months },
      colors: ['#ffab00'],
      fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 90, 100] } },
      tooltip: { theme: 'dark' },
      legend: { position: 'top' }
    };
    var chart = new ApexCharts(document.getElementById('adminMonthlyChart'), options);
    chart.render();
  })();
</script>
@endslot
@endcomponent
