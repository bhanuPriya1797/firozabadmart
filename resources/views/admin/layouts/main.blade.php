<!doctype html>
<html
  lang="en"
  class="layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-skin="default"
  data-assets-path="{{ asset('admin/assets') }}/"
  data-template="vertical-menu-template"
  data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <meta name="_token" content="{{ csrf_token() }}" />
    @php
        use Illuminate\Support\Facades\Storage;
        use App\Helpers\CustomHelper;

        $metaValues = CustomHelper::getSettings(['meta_title', 'meta_description', 'meta_keywords', 'favicon']);

        $defaultFavicon = asset('admin/assets/img/favicon/favicon.jpeg');
        $faviconPath = $defaultFavicon;

        if (!empty($metaValues['favicon']) && Storage::disk('public')->exists($metaValues['favicon'])) {
            $faviconPath = asset('storage/' . $metaValues['favicon']);
        }
    @endphp

    <title>{{ $title ?? ($metaValues['meta_title'] ?? '') . ' - Admin Panel' }}</title>
    <meta name="description" content="{{ $metaValues['meta_description'] ?? '' }}" />
    <meta name="keywords" content="{{ $metaValues['meta_keywords'] ?? '' }}" />

    <!-- Favicon -->
    <link rel="icon" href="{{ $faviconPath }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/iconify-icons.css') }}" />
    <!-- <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/node-waves/node-waves.css') }}" /> -->
    <!-- <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/pickr/pickr-themes.css') }}" /> -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/demo.css') }}" />
    <!-- Custom Admin Theme Overrides -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/custom-admin-theme.css') }}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />

    <!-- endbuild -->

    <!-- <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/apex-charts/apex-charts.css') }}" /> -->
    <!-- <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/swiper/swiper.css') }}" /> -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <!-- <link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/flag-icons.css') }}" /> -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/fontawesome.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/pages/cards-advance.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('admin/assets/vendor/js/helpers.js') }}"></script>
    <!-- <script src="{{ asset('admin/assets/vendor/js/template-customizer.js') }}"></script> -->
    <script src="{{ asset('admin/assets/js/config.js') }}"></script>
    <!--Header block-->
    {{ $headerBlock ?? '' }}
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
	    <!-- Menu (Sidebar)-->
	    @include('admin.layouts.sidebar')

        <div class="menu-mobile-toggler d-xl-none rounded-1">
          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
            <i class="ti tabler-menu icon-base"></i>
            <i class="ti tabler-chevron-right icon-base"></i>
          </a>
        </div>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          @include('admin.layouts.top')
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            {{ $slot }}
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl">
                <div
                  class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                  <div class="text-body">
                    ©
                    <script>
                      document.write(new Date().getFullYear());
                    </script>
                    , All Right Reserverd By <a href="{{ env('APP_URL') }}" target="_blank" class="footer-link">{{ $metaValues['meta_title'] }}</a>
                  </div>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>

          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js -->

    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> 
    <script src="{{ asset('admin/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>
    <!-- <script src="{{ asset('admin/assets/vendor/libs/pickr/pickr.js') }}"></script> -->
    <script src="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <!-- <script src="{{ asset('admin/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script> -->
    <!-- <script src="{{ asset('admin/assets/vendor/libs/swiper/swiper.js') }}"></script> -->
    <script src="{{ asset('admin/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <!-- Main JS -->

    <script src="{{ asset('admin/assets/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
      if (window.Swal && !window.swal) {
        window.swal = function() {
          var args = Array.prototype.slice.call(arguments);
          if (args.length === 1 && typeof args[0] === 'object') {
            return window.Swal.fire(args[0]);
          }
          var title = args[0], text = args[1], icon = args[2];
          return window.Swal.fire({ title: title, text: text, icon: icon });
        };
      }
    </script>

    <!-- Page JS -->
    <script src="{{ asset('admin/assets/js/dashboards-analytics.js') }}"></script>
    <script src="{{ asset('admin/assets/js/media-selector.js') }}"></script>
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    
	<!--Bottom block-->
	{{ $footerBlock ?? '' }}
    <script>
    (function(){
      if(!window.jQuery) return;
      var uploadJsonUrl = '{{ route(App\Helpers\CustomHelper::getAdminRouteName() . ".ck_upload_json") }}';
      var adminMediaIndexUrl = '{{ route(App\Helpers\CustomHelper::getAdminRouteName() . ".media.index") }}';
      var browseUrl = '{{ route(App\Helpers\CustomHelper::getAdminRouteName() . ".media.index") }}?popup=1';
      var csrf = '{{ csrf_token() }}';
      window.ADMIN_MEDIA_INDEX_URL = adminMediaIndexUrl;
      window.ADMIN_PREFIX = '{{ App\Helpers\CustomHelper::getAdminRouteName() }}';
      
      // Configure CKEditor to use Media Manager
      if(window.CKEDITOR) {
          CKEDITOR.config.filebrowserBrowseUrl = browseUrl;
          CKEDITOR.config.filebrowserImageBrowseUrl = browseUrl;
          CKEDITOR.config.filebrowserUploadUrl = uploadJsonUrl; 
          CKEDITOR.config.filebrowserImageUploadUrl = uploadJsonUrl;
          
          // Ensure the "Browse Server" button appears
          CKEDITOR.on('dialogDefinition', function(ev) {
              var dialogName = ev.data.name;
              var dialogDefinition = ev.data.definition;
              if (dialogName == 'image') {
                  // The 'Link' tab is not needed
                  // dialogDefinition.removeContents('Link');
                  // The 'Advanced' tab is not needed
                  // dialogDefinition.removeContents('advanced');
              }
          });
      }

      var overlayHtml = '<div id="imageLibraryOverlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:none;"><div style="max-width:900px;margin:40px auto;background:#fff;border-radius:6px;overflow:hidden"><div style="padding:10px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center"><strong>Image Library</strong><button type="button" id="closeImageLibrary" class="btn btn-sm btn-outline-secondary">Close</button></div><iframe id="imageLibraryFrame" src="" style="width:100%;height:520px;border:0"></iframe></div></div>';
      if(!document.getElementById('imageLibraryOverlay')){ document.body.insertAdjacentHTML('beforeend', overlayHtml); }
      function insertInto(editorId, url){ if(window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances[editorId]){ CKEDITOR.instances[editorId].insertHtml('<img src="'+url+'" />'); } }
      window.insertImageFromLibrary = function(url){ var id = window.currentCkEditorInstanceId; if(id){ insertInto(id, url); } var ov=document.getElementById('imageLibraryOverlay'); if(ov) ov.style.display='none'; };
      function addToolsForEditor(editorId){
        var el = document.getElementById(editorId);
        if(!el) return;
        if($(el).nextAll().find('[id$="OpenLibrary"]').length>0) return;
        if($(el).attr('data-gallery-tools')==='1') return;
        var quickId = editorId+'QuickUpload';
        var openId = editorId+'OpenLibrary';
        var toolsHtml = '<div class="mt-2 ck-gallery-tools"><div class="d-flex gap-2 align-items-center"><input type="file" id="'+quickId+'" class="form-control form-control-sm" accept="image/*" style="max-width:260px"><button type="button" class="btn btn-sm btn-outline-primary" id="'+openId+'">Choose From Library</button></div></div>';
        $(el).after(toolsHtml);
        $(el).attr('data-gallery-tools','1');
        $(document).off('change','#'+quickId).on('change','#'+quickId,function(){ var f=this.files && this.files[0]; if(!f) return; var fd=new FormData(); fd.append('upload', f); fd.append('_token', csrf); fetch(uploadJsonUrl,{method:'POST', body:fd}).then(function(r){return r.json();}).then(function(j){ if(j && j.success && j.url){ insertInto(editorId, j.url); } }); });
        $(document).off('click','#'+openId).on('click','#'+openId,function(){ window.currentCkEditorInstanceId = editorId; var ov=document.getElementById('imageLibraryOverlay'); var fr=document.getElementById('imageLibraryFrame'); if(ov && fr){ fr.src = browseUrl; ov.style.display='block'; } });
        $(document).off('click','#closeImageLibrary').on('click','#closeImageLibrary',function(){ var ov=document.getElementById('imageLibraryOverlay'); if(ov){ ov.style.display='none'; } });
        $(document).off('click','#imageLibraryOverlay').on('click','#imageLibraryOverlay',function(e){ if(e.target && e.target.id==='imageLibraryOverlay'){ this.style.display='none'; } });
      }
      if (window.CKEDITOR) {
        CKEDITOR.on('instanceReady', function(evt){ var editorId = evt.editor.name; addToolsForEditor(editorId); evt.editor.on('focus', function(){ window.currentCkEditorInstanceId = editorId; }); });
        for(var id in CKEDITOR.instances){ if(CKEDITOR.instances.hasOwnProperty(id)) addToolsForEditor(id); }
      }
    })();
    </script>
  <script>
      toastr.options = {
          closeButton: true,
          progressBar: true,
          timeOut: "5000",
          positionClass: "toast-top-right"
      };
  </script>
  <div id="flash-messages" style="display:none"
       data-success="{{ session('success') }}"
       data-error="{{ session('error') }}"
       data-info="{{ session('info') }}"
       data-warning="{{ session('warning') }}"></div>
  <script>
    (function(){
      var el=document.getElementById('flash-messages');
      if(!el) return;
      var s=el.getAttribute('data-success');
      var e=el.getAttribute('data-error');
      var i=el.getAttribute('data-info');
      var w=el.getAttribute('data-warning');
      if(s) toastr.success(s);
      if(e) toastr.error(e);
      if(i) toastr.info(i);
      if(w) toastr.warning(w);
    })();
  </script>
</body>
</html>
