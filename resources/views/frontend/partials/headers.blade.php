@php
    $settings = \App\Helpers\CustomHelper::getSettings(['logo_header','website_name','home_menu_slug']);
    $menuSlug = $settings['home_menu_slug'] ?? 'main-menu';
    $menuItems = \App\Helpers\MenuHelper::getMenuForFrontend($menuSlug);
    $logo = !empty($settings['logo_header']) ? asset('storage/' . $settings['logo_header']) : asset('frontend/img/general/logo.png');
@endphp
<header class="header simple-header">
  <div class="inner">
    <a href="{{ url('/') }}" class="simple-logo">
      <img src="{{ $logo }}" alt="{{ $settings['website_name'] ?? 'Logo' }}">
    </a>
    <button class="simple-toggle" aria-label="Open menu">☰</button>
    <nav class="simple-nav">
      @php
        echo \App\Helpers\MenuHelper::buildMenuHtml($menuItems, [
            'parent_class' => 'menu__nav',
            'item_class' => '',
            'link_class' => '',
            'active_class' => 'active',
            'dropdown_class' => 'menu-item-has-children',
            'dropdown_toggle_class' => '',
            'dropdown_menu_class' => 'subnav'
        ]);
      @endphp
    </nav>
  </div>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  var btn = document.querySelector('.simple-toggle');
  var nav = document.querySelector('.simple-nav');
  if (btn && nav) {
    btn.addEventListener('click', function(){
      nav.classList.toggle('is-open');
    });
  }
});
</script>
@endpush
