@component('admin.layouts.main')

@slot('title')
    {{ $page_heading }} - {{ config('app.name') }}
@endslot

@slot('headerBlock')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        /* Vuexy Theme Compatible Styles */
        .menu-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #fff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 0.25rem 1.125rem rgba(75, 85, 99, 0.08);
            border: 1px solid rgba(75, 85, 99, 0.08);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1.5rem rgba(75, 85, 99, 0.12);
        }

        .stat-number {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--bs-primary);
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stat-label {
            color: #6c757d;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .sortable-menu-container {
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0.25rem 1.125rem rgba(75, 85, 99, 0.08);
            border: 1px solid rgba(75, 85, 99, 0.08);
            overflow: hidden;
        }

        .sortable-menu-header {
            background: var(--bs-primary);
            color: #fff;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sortable-menu-body {
            padding: 2rem;
        }

        .sortable_menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sortable_menu li {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .sortable_menu li:hover {
            border-color: var(--bs-primary);
            box-shadow: 0 0.25rem 0.75rem rgba(75, 85, 99, 0.1);
        }

        .sortable_menu li.ui-sortable-helper {
            box-shadow: 0 0.5rem 1.5rem rgba(75, 85, 99, 0.15);
            transform: rotate(2deg);
        }

        .sortable_menu li.ui-sortable-placeholder {
            background: rgba(var(--bs-primary-rgb), 0.1);
            border: 2px dashed var(--bs-primary);
            visibility: visible !important;
        }

        .menu-item-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            gap: 1rem;
        }

        .menu-item-drag {
            cursor: move;
            color: #6c757d;
            font-size: 1.25rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }

        .menu-item-drag:hover {
            background: rgba(var(--bs-primary-rgb), 0.1);
            color: var(--bs-primary);
        }

        .menu-item-details {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .menu-item-icon {
            width: 40px;
            height: 40px;
            background: var(--bs-primary);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.1rem;
        }

        .menu-item-text {
            flex: 1;
        }

        .menu-item-name {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.25rem;
        }

        .menu-item-url {
            font-size: 0.875rem;
            color: #6c757d;
            word-break: break-all;
        }

        .menu-item-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            border: none;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }

        .btn-edit {
            background: rgba(var(--bs-primary-rgb), 0.1);
            color: var(--bs-primary);
        }

        .btn-edit:hover {
            background: var(--bs-primary);
            color: #fff;
        }

        .btn-delete {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .btn-delete:hover {
            background: #dc3545;
            color: #fff;
        }

        .nested-menu {
            list-style: none;
            padding-left: 2rem;
            margin-top: 0.5rem;
            border-left: 2px solid #e9ecef;
        }

        .nested-menu li {
            margin-bottom: 0.25rem;
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(75, 85, 99, 0.08);
        }

        .form-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-section-title i {
            color: var(--bs-primary);
        }

        .form-floating {
            margin-bottom: 1rem;
        }

        .form-floating > .form-control,
        .form-floating > .form-select {
            height: calc(3.5rem + 2px);
            line-height: 1.25;
        }

        .form-floating > label {
            padding: 1rem 0.75rem;
        }

        .btn-save-order {
            background: var(--bs-success);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-save-order:hover {
            background: #198754;
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.75rem rgba(25, 135, 84, 0.3);
        }

        @media (max-width: 768px) {
            .menu-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .menu-item-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .menu-item-actions {
                align-self: flex-end;
            }

            .nested-menu {
                padding-left: 1rem;
            }
        }
    </style>
@endslot

<?php
use App\Helpers\MenuHelper;

$routeName = CustomHelper::getAdminRouteName();
$backUrl = CustomHelper::BackUrl();
$id = $menu->id ?? '';
$menuItemId = $menuItem->id ?? '';
$title = $menuItem->title ?? '';
$link_type = $menuItem->link_type ?? '';
$page_id = $menuItem->page_id ?? '';
$url = $menuItem->url ?? '';
$target = $menuItem->target ?? '';
$status = $menuItem->status ?? 1;
$menu_link_type_arr = config('custom.menu_link_type_arr');
$targetArr = ['_self' => 'Same Window/Tab', '_blank' => 'New Window/Tab'];
$menuItems = $menu->menuParentItems ?? '';
$menuItemsList = CustomHelper::getMenuItemsList($menuItems, $id, true, 'sortable_menu list-group', 'sortable list-group');

// Get menu statistics
$menuStats = MenuHelper::getMenuStats($menu->id);
?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ $page_heading }}</h4>
            <p class="text-muted mb-0">Manage menu items and their hierarchy</p>
        </div>
        <a href="{{ url($routeName . '/menus') }}" class="btn btn-outline-secondary">
            <i class="ti tabler-arrow-left me-2"></i>Back to Menus
        </a>
    </div>

    <!-- Menu Statistics -->
    <div class="menu-stats">
        <div class="stat-card">
            <div class="stat-number">{{ $menuStats['total'] }}</div>
            <div class="stat-label">Total Items</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $menuStats['active'] }}</div>
            <div class="stat-label">Active Items</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $menuStats['parents'] }}</div>
            <div class="stat-label">Parent Items</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $menuStats['children'] }}</div>
            <div class="stat-label">Child Items</div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Menu Item Form -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti tabler-plus me-2"></i>
                        {{ $menuItemId ? 'Edit Menu Item' : 'Add New Menu Item' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        @csrf

                        <!-- Basic Information -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="ti tabler-info-circle"></i>
                                Basic Information
                            </div>
                            
                            <div class="form-floating">
                                <input type="text" name="title" class="form-control" id="title" value="{{ old('title', $title) }}" placeholder="Menu Item Title">
                                <label for="title">Menu Item Title *</label>
                            </div>

                            <div class="form-floating">
                                <select name="link_type" class="form-select" id="link_type">
                                    @foreach($menu_link_type_arr as $ltKey => $ltVal)
                                        <option value="{{ $ltKey }}" {{ $ltKey == $link_type ? 'selected' : '' }}>{{ $ltVal }}</option>
                                    @endforeach
                                </select>
                                <label for="link_type">Link Type *</label>
                            </div>
                        </div>

                        <!-- Page Selection -->
                        <div class="form-section pageBoxDiv">
                            <div class="form-section-title">
                                <i class="ti tabler-link"></i>
                                Page Selection
                            </div>
                            <div class="pageBox">
                                <div class="form-floating">
                                    <select name="page_id" class="form-select" id="page_id">
                                        <option value="">Select a page...</option>
                                    </select>
                                    <label for="page_id">Select Page</label>
                                </div>
                            </div>
                        </div>

                        <!-- URL Configuration -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="ti tabler-world"></i>
                                URL Configuration
                            </div>
                            
                            <div class="form-floating">
                                <input type="text" name="url" class="form-control" id="url" value="{{ old('url', $url) }}" placeholder="URL">
                                <label for="url">URL</label>
                            </div>

                            <div class="form-floating">
                                <select name="target" class="form-select" id="target">
                                    @foreach($targetArr as $tKey => $tVal)
                                        <option value="{{ $tKey }}" {{ $tKey == $target ? 'selected' : '' }}>{{ $tVal }}</option>
                                    @endforeach
                                </select>
                                <label for="target">Target</label>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="ti tabler-toggle-right"></i>
                                Status
                            </div>
                            
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" value="1" id="status_active" {{ $status == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_active">
                                        <i class="ti tabler-check text-success me-1"></i>Active
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" value="0" id="status_inactive" {{ $status == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_inactive">
                                        <i class="ti tabler-x text-danger me-1"></i>Inactive
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2">
                            <input type="hidden" name="id" value="{{ old('id', $id) }}">
                            @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('menus.edit')))
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="ti tabler-device-floppy me-2"></i>
                                {{ $menuItemId ? 'Update' : 'Create' }} Menu Item
                            </button>
                            @endif
                            @if(is_numeric($menuItemId) && $menuItemId > 0)
                                <a href="{{ url($routeName . '/menus/items/' . $menu->id) }}" class="btn btn-outline-secondary">
                                    <i class="ti tabler-x me-2"></i>Cancel
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Menu Items List -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti tabler-list me-2"></i>
                        Menu Items Structure
                    </h5>
                    <p class="mb-0 mt-1 opacity-75">Drag and drop to reorder menu items</p>
                </div>
                <div class="card-body">
                    {!! $menuItemsList !!}
                    
                    <div class="text-center mt-4">
                        @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('menus.edit')))
                        <button type="button" class="btn btn-success saveMenuItems">
                            <i class="ti tabler-device-floppy me-2"></i>
                            Save Menu Order
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@slot('footerBlock')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="{{ asset('admin/assets/js/jquery.mjs.nestedSortable.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $('.sortable_menu').nestedSortable({
            items: 'li',
            handle: '.handle',
            maxLevels: 4,
            placeholder: 'sortable-placeholder',
            forcePlaceholderSize: true,
            opacity: .6,
            toleranceElement: '> div',
            listType: 'ol',
            tabSize: 25,
            disableNesting: 'no-nest',
            expandOnHover: 700,
            startCollapsed: false
        });

        populatePageList();

        $(document).on("change", "select[name='link_type']", populatePageList);
        $(document).on("change", "select[name='page_id']", populateUrl);

        function populatePageList() {
            const link_type = $("select[name='link_type']").val();
            const page_id = '{{ $page_id }}';
            const pageUrl = '{{ $url }}';

            if (link_type === 'internal' || link_type === 'external' || link_type === 'custom') {
                $(".pageBox").html('<div class="form-floating"><input type="text" name="page_id" value="" class="form-control" id="page_id" placeholder="Page ID" /><label for="page_id">Page ID</label></div>');
                $(".pageBoxDiv").hide();
                $("input[name='url']").val('').prop("readonly", false);
            } else {
                $(".pageBox").html('<div class="form-floating"><select name="page_id" class="form-select" id="page_id"><option value="">Select a page...</option></select><label for="page_id">Select Page</label></div>');
                $(".pageBoxDiv").show();

                $.post("{{ route($routeName.'.menus.ajax_get_link_type_list') }}", {
                    link_type: link_type,
                    page_id: page_id,
                    _token: '{{ csrf_token() }}'
                }, function(resp) {
                    if (resp.success && resp.list) {
                        $(".pageBox").html(resp.list);
                        populateUrl();
                    }
                }, "json");
            }
        }

        function populateUrl() {
            const page = $("select[name='page_id']");
            const page_id = page.val();
            const url = page.find("option[value='" + page_id + "']").data("url");

            if (url) {
                $("input[name='url']").val(url).prop("readonly", true);
            }
        }

        // Save Menu Items Sort Order
        $('.saveMenuItems').click(function () {
            const $btn = $(this);
            const originalText = $btn.html();
            
            $btn.html('<i class="ti tabler-loader-2 ti-spin me-2"></i>Saving...').prop('disabled', true);
            
            // Manual serialization - this is more reliable
            const serialized = {};
            let order = 1;
            
            // Get all menu items in their current order
            $('ol.sortable_menu li').each(function() {
                const itemId = $(this).attr('id').replace('item_id_', '');
                const parentLi = $(this).parent('ol').parent('li');
                const parentId = parentLi.length > 0 ? parentLi.attr('id').replace('item_id_', '') : 'null';
                
                serialized['item_id[' + itemId + ']'] = 'item_id[' + itemId + ']';
                serialized['parent_id[' + itemId + ']'] = parentId === 'null' ? 'null' : 'parent_id[' + parentId + ']';
                serialized['order[' + itemId + ']'] = order++;
            });
            
            console.log('Manual serialized data:', serialized);
            
            $.post("{{ route($routeName.'.menus.ajax_update_items') }}", serialized)
                .done(function (resp) {
                    toastr.success('Menu items order updated successfully!');
                    $btn.html(originalText).prop('disabled', false);
                    
                    // Refresh the page after a short delay to show updated order
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                })
                .fail(function (xhr, status, error) {
                    console.error('Error response:', xhr.responseText);
                    toastr.error('Failed to update menu items order.');
                    $btn.html(originalText).prop('disabled', false);
                });
        });

        $(document).on("click", ".delItem", function () {
            const id = $(this).data("id");

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route($routeName.'.menus.ajax_delete_item') }}", { id: id })
                        .done(function (resp) {
                            toastr.success('Menu item deleted successfully!');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        })
                        .fail(function () {
                            toastr.error('Failed to delete menu item.');
                        });
                }
            });
        });
    </script>
@endslot

@endcomponent
