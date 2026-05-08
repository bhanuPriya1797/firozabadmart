@php
$ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
$settings = \App\Helpers\CustomHelper::getSettings(['admin_logo', 'favicon', 'website_name']);
$unreadEnquiries = \App\Models\Enquiry::unread()->count();
$unreadEnquiriesTotal = $unreadEnquiries;
@endphp
<aside id="layout-menu" class="layout-menu menu-vertical menu">
  <div class="app-brand demo justify-content-center align-items-center">
    <a href="{{ route($ADMIN_ROUTE_NAME.'.index') }}" class="app-brand-link d-flex flex-column align-items-center w-100">
      <span class="app-brand-logo demo logo-wrapper">
        @if(!empty($settings['admin_logo']))
            <img src="{{ asset('storage/' . $settings['admin_logo']) }}" alt="{{ $settings['website_name'] ?? '' }} Logo" class="custom-logo">
        @else
            <img src="{{ asset('admin/assets/img/logo.png') }}" alt="Admin Logo" class="custom-logo">
        @endif
      </span>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">

    <!-- Dashboard -->
    @hasPermission('dashboard.view')
    <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.index') === 0 ? "active open" : "" !!}">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-home"></i>
        <div data-i18n="Dashboard">Dashboard</div>
      </a>
    </li>
    @endHasPermission

    <!-- Activity Logs -->
    @hasPermission('activities.view')
    <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.activities.index') === 0 ? "active open" : "" !!}">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.activities.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-clipboard-list"></i>
        <div data-i18n="Activity Logs">Activity Logs</div>
      </a>
    </li>
    @endHasPermission

    <!-- User Management -->
    @hasAnyPermission(['users.view', 'users.create', 'users.edit', 'users.delete', 'roles.view', 'roles.create', 'roles.edit', 'roles.delete'])
    <li class="menu-item {!! (strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.users.index') === 0 || strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.roles.index') === 0) ? "active open" : "" !!}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-user-cog"></i>
        <div data-i18n="User Management">User Management</div>
      </a>
      <ul class="menu-sub">
        @hasPermission('users.view')
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.users.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.users.index') }}" class="menu-link">
            <div data-i18n="Users">Users</div>
          </a>
        </li>
        @endHasPermission
        @hasPermission('roles.view')
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.roles.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.roles.index') }}" class="menu-link">
            <div data-i18n="Roles & Permissions">Roles & Permissions</div>
          </a>
        </li>
        @endHasPermission
      </ul>
    </li>
    @endHasAnyPermission

    <!-- CMS (Pages) -->
    @hasAnyPermission(['cms.view', 'cms.create', 'cms.edit', 'cms.delete'])
    <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.cms.index') === 0 ? "active open" : "" !!}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-file-description"></i>
        <div data-i18n="CMS Pages">CMS Pages</div>
      </a>
      <ul class="menu-sub">
        @hasPermission('cms.view')
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.cms.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.cms.index') }}" class="menu-link">
            <div data-i18n="Pages">Pages</div>
          </a>
        </li>
        @endHasPermission
      </ul>
    </li>
    @endHasAnyPermission

    <!-- Menus -->
    @hasAnyPermission(['menus.view', 'menus.create', 'menus.edit', 'menus.delete', 'menu_items.manage'])
    <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.menus.index') === 0 ? "active open" : "" !!}">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.menus.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-layout-navbar-expand"></i>
        <div data-i18n="Menu Management">Menu Management</div>
      </a>
    </li>
    @endHasAnyPermission

    <!-- Content Management -->
    @hasAnyPermission(['blog_categories.view', 'blog_categories.create', 'blog_categories.edit', 'blog_categories.delete', 'blogs.view', 'blogs.create', 'blogs.edit', 'blogs.delete', 'news.view', 'news.create', 'news.edit', 'news.delete', 'success_stories.view', 'success_stories.create', 'success_stories.edit', 'success_stories.delete', 'partners.view', 'partners.create', 'partners.edit', 'partners.delete', 'team_members.view', 'team_members.create', 'team_members.edit', 'team_members.delete', 'calendar.view', 'calendar.create', 'calendar.edit', 'calendar.delete'])
    <li class="menu-item {!! (strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.blogs_categories') === 0 || strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.blogs') === 0 || strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.news') === 0 || strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.events') === 0 || strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.success-stories') === 0 || strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.partners') === 0) ? "active open" : "" !!}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-brand-blogger"></i>
        <div data-i18n="Content Management">Content Management</div>
      </a>
      <ul class="menu-sub">
        {{-- 
        @hasAnyPermission(['blog_categories.view', 'blog_categories.create', 'blog_categories.edit', 'blog_categories.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.blogs_categories.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.blogs_categories.index') }}" class="menu-link">
            <div data-i18n="Categories">Categories</div>
          </a>
        </li>
        @endHasAnyPermission
        --}}
        {{--
        @hasAnyPermission(['blogs.view', 'blogs.create', 'blogs.edit', 'blogs.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.blogs.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.blogs.index') }}" class="menu-link">
            <div data-i18n="Blogs">Blogs</div>
          </a>
        </li>
        @endHasAnyPermission
        --}}
        {{--
        @hasAnyPermission(['blog_comments.view'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.blog-comments.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.blog-comments.index') }}" class="menu-link">
            <div data-i18n="Comments">Comments</div>@if($unreadComments>0)<span class="badge bg-danger ms-2">{{ $unreadComments }}</span>@endif
          </a>
        </li>
        @endHasAnyPermission
        --}}
        
        @hasAnyPermission(['news.view', 'news.create', 'news.edit', 'news.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.news.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.news.index') }}" class="menu-link">
            <div data-i18n="News">News</div>
          </a>
        </li>
        @endHasAnyPermission
        
        @hasAnyPermission(['events.view', 'events.create', 'events.edit', 'events.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.events.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.events.index') }}" class="menu-link">
            <div data-i18n="Events">Events</div>
          </a>
        </li>
        @endHasAnyPermission

        @hasAnyPermission(['partners.view', 'partners.create', 'partners.edit', 'partners.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.partners.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.partners.index') }}" class="menu-link">
            <div data-i18n="Partners">Partners</div>
          </a>
        </li>
        @endHasAnyPermission
        
        @hasAnyPermission(['achievements.view', 'achievements.create', 'achievements.edit', 'achievements.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.achievements.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.achievements.index') }}" class="menu-link">
            <div data-i18n="Achievements">Achievements</div>
          </a>
        </li>
        @endHasAnyPermission
        
        @hasAnyPermission(['circulars.view', 'circulars.create', 'circulars.edit', 'circulars.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.circulars.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.circulars.index') }}" class="menu-link">
            <div data-i18n="Circulars">Circulars</div>
          </a>
        </li>
        @endHasAnyPermission
        @hasAnyPermission(['calendar.view', 'calendar.create', 'calendar.edit', 'calendar.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.calendar.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.calendar.index') }}" class="menu-link">
            <div data-i18n="Calendar">Calendar</div>
          </a>
        </li>
        @endHasAnyPermission
        @hasAnyPermission(['success_stories.view', 'success_stories.create', 'success_stories.edit', 'success_stories.delete'])
            <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.success-stories.index') === 0 ? "active" : "" !!}">
              <a href="{{ route($ADMIN_ROUTE_NAME.'.success-stories.index') }}" class="menu-link">
                <div data-i18n="Testimonial">Testimonial</div>
              </a>
            </li>
        
        @endHasAnyPermission
        @hasAnyPermission(['team_members.view', 'team_members.create', 'team_members.edit', 'team_members.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.team-members.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.team-members.index') }}" class="menu-link">
            <div data-i18n="Team Members">Team Members</div>
          </a>
        </li>
        @endHasAnyPermission
      </ul>
    </li>
    @endHasAnyPermission

    <!-- Banner Management -->
    @hasAnyPermission(['banners.view', 'banners.create', 'banners.edit', 'banners.delete', 'banners.media_manage'])
    <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.banners') === 0 ? "active open" : "" !!}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-photo"></i>
        <div data-i18n="Banner Management">Banner Management</div>
      </a>
      <ul class="menu-sub">
        @hasPermission('banners.view')
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.banners.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.banners.index') }}" class="menu-link">
            <div data-i18n="All Banners">All Banners</div>
          </a>
        </li>
        @endHasPermission
        @hasPermission('banners.create')
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.banners.add') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.banners.add') }}" class="menu-link">
            <div data-i18n="Add Banner">Add Banner</div>
          </a>
        </li>
        @endHasPermission
      </ul>
    </li>
    @endHasAnyPermission
    
    @hasAnyPermission(['gallery.view', 'gallery.create', 'gallery.edit', 'gallery.delete'])
    <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.gallery') === 0 ? "active open" : "" !!}">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.gallery.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-photo"></i>
        <div data-i18n="Gallery">Gallery</div>
      </a>
    </li>
    @endHasAnyPermission

    @hasAnyPermission(['media.view', 'media.upload', 'media.edit', 'media.delete'])
    <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.media') === 0 ? "active open" : "" !!}">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.media.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-photo"></i>
        <div data-i18n="Media Manager">Media Manager</div>
      </a>
    </li>
    @endHasAnyPermission

    @hasAnyPermission(['archers.view', 'archers.create', 'archers.edit', 'archers.delete'])
    <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.archers') === 0 ? "active open" : "" !!}">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.archers.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-users"></i>
        <div data-i18n="Archers">Archers</div>
      </a>
    </li>
    @endHasAnyPermission

    <!-- FAQ Management -->
    <!-- @hasAnyPermission(['faq_categories.view', 'faq_categories.create', 'faq_categories.edit', 'faq_categories.delete', 'faqs.view', 'faqs.create', 'faqs.edit', 'faqs.delete'])
    <li class="menu-item {!! (strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.faq-categories') === 0 || strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.faqs') === 0 ) ? "active open" : "" !!}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-help"></i>
        <div data-i18n="FAQ Management">FAQ Management</div>
      </a>
      <ul class="menu-sub">
        @hasAnyPermission(['faq_categories.view', 'faq_categories.create', 'faq_categories.edit', 'faq_categories.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.faq-categories.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.faq-categories.index') }}" class="menu-link">
            <div data-i18n="FAQ Categories">FAQ Categories</div>
          </a>
        </li>
        @endHasAnyPermission
        @hasAnyPermission(['faqs.view', 'faqs.create', 'faqs.edit', 'faqs.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.faqs.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.faqs.index') }}" class="menu-link">
            <div data-i18n="FAQs">FAQs</div>
          </a>
        </li>
        @endHasAnyPermission
      </ul>
    </li>
    @endHasAnyPermission -->

    <!-- Enquiries Management -->
    @hasAnyPermission(['enquiries.view', 'enquiries.create', 'enquiries.edit', 'enquiries.delete', 'volunteer_applications.view', 'volunteer_applications.create', 'volunteer_applications.edit', 'volunteer_applications.delete', 'newsletter.view', 'newsletter.create', 'newsletter.edit', 'newsletter.delete'])
    <li class="menu-item {!! (strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.newsletter') === 0 || strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.enquiries') === 0 || strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.volunteer-applications') === 0 ) ? "active open" : "" !!}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-phone"></i>
        <div data-i18n="Enquiries Management">Enquiries Management</div>@if($unreadEnquiriesTotal>0)<span class="badge bg-danger ms-2">{{ $unreadEnquiriesTotal }}</span>@endif
      </a>
      <ul class="menu-sub">
        @hasAnyPermission(['enquiries.view', 'enquiries.create', 'enquiries.edit', 'enquiries.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.enquiries.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.enquiries.index') }}" class="menu-link">
            <div data-i18n="Contact Enquiries">Contact Enquiries</div>@if($unreadEnquiries>0)<span class="badge bg-danger ms-2">{{ $unreadEnquiries }}</span>@endif
          </a>
        </li>
        @endHasAnyPermission
        

        @hasAnyPermission(['newsletter.view', 'newsletter.create', 'newsletter.edit', 'newsletter.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.newsletter.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.newsletter.index') }}" class="menu-link">
            <div data-i18n="Newsletters">Newsletters</div>
          </a>
        </li>
        @endHasAnyPermission
      </ul>
    </li>
    @endHasAnyPermission

    <!-- Settings -->
    @hasAnyPermission(['settings.view', 'settings.edit', 'custom_fields.view', 'custom_fields.create', 'custom_fields.edit', 'custom_fields.delete'])
    <li class="menu-item {!! (strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.settings.index') === 0 || strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.custom_fields') === 0) ? "active open" : "" !!}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-settings"></i>
        <div data-i18n="Settings">Settings</div>
      </a>
      <ul class="menu-sub">
        @hasAnyPermission(['settings.view', 'settings.edit'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.settings.index') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.settings.index') }}" class="menu-link">
            <div data-i18n="Website Settings">Website Settings</div>
          </a>
        </li>
        @endHasAnyPermission
        @hasAnyPermission(['custom_fields.view', 'custom_fields.create', 'custom_fields.edit', 'custom_fields.delete'])
        <li class="menu-item {!! strpos(Route::currentRouteName(), $ADMIN_ROUTE_NAME.'.custom_fields') === 0 ? "active" : "" !!}">
          <a href="{{ route($ADMIN_ROUTE_NAME.'.custom_fields.index') }}" class="menu-link">
            <div data-i18n="Custom Fields">Custom Fields</div>
          </a>
        </li>
        @endHasAnyPermission
      </ul>
    </li>
    @endHasAnyPermission

  </ul>
</aside>
