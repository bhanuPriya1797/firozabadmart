<?php

namespace App\Helpers;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Cms;
use App\Models\Blog;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class MenuHelper
{
    /**
     * Get menu by slug with caching
     */
    public static function getMenu($slug, $status = 1)
    {
        $cacheKey = "menu_{$slug}_{$status}";
        
        return Cache::remember($cacheKey, 3600, function () use ($slug, $status) {
            return Menu::where(['slug' => $slug, 'status' => $status])->first();
        });
    }

    /**
     * Get menu items with hierarchy
     */
    public static function getMenuItems($menuId, $parentId = 0, $status = 1)
    {
        $cacheKey = "menu_items_{$menuId}_{$parentId}_{$status}";
        
        return Cache::remember($cacheKey, 3600, function () use ($menuId, $parentId, $status) {
            return MenuItem::where([
                'menu_id' => $menuId,
                'parent_id' => $parentId,
                'status' => $status
            ])
            ->orderBy('sort_order', 'asc')
            ->with('children')
            ->get();
        });
    }

    /**
     * Get menu items for frontend with full hierarchy
     */
    public static function getMenuForFrontend($slug, $status = 1)
    {
        $menu = self::getMenu($slug, $status);
        
        if (!$menu) {
            return collect();
        }

        return self::getMenuItems($menu->id, 0, $status);
    }

    /**
     * Build menu HTML for frontend
     */
    public static function buildMenuHtml($menuItems, $options = [])
    {
        $defaultOptions = [
            'parent_class' => 'nav-menu',
            'child_class' => 'sub-menu',
            'item_class' => 'nav-item',
            'link_class' => 'nav-link',
            'active_class' => 'active',
            'dropdown_class' => 'dropdown',
            'dropdown_toggle_class' => 'dropdown-toggle',
            'dropdown_menu_class' => 'dropdown-menu',
        ];

        $options = array_merge($defaultOptions, $options);
        
        if ($menuItems->isEmpty()) {
            return '';
        }

        $html = "<ul class=\"{$options['parent_class']}\">";
        
        foreach ($menuItems as $item) {
            $html .= self::buildMenuItemHtml($item, $options);
        }
        
        $html .= "</ul>";
        
        return $html;
    }

    /**
     * Build individual menu item HTML
     */
    private static function buildMenuItemHtml($item, $options)
    {
        $url = self::getMenuItemUrl($item);
        $hasChildren = $item->children && $item->children->count() > 0;
        
        $itemClass = $options['item_class'];
        $linkClass = $options['link_class'];
        $activeClass = $options['active_class'] ?? 'active';
        $currentUrl = request()->url();
        $isActive = ($url === $currentUrl);
        
        if ($hasChildren) {
            $itemClass .= " {$options['dropdown_class']}";
            $linkClass .= " {$options['dropdown_toggle_class']}";
        }

        if ($isActive && !empty($activeClass)) {
            $linkClass .= " {$activeClass}";
        }

        $html = "<li class=\"{$itemClass}\">";
        $toggle = $hasChildren ? ' data-bs-toggle="dropdown" role="button" aria-expanded="false"' : '';
        $aria = $isActive ? ' aria-current="page"' : '';
        $html .= "<a href=\"{$url}\" class=\"{$linkClass}\" target=\"{$item->target}\"{$toggle}{$aria}>";
        $html .= htmlspecialchars($item->title);
        
        if ($hasChildren) {
            $html .= " <i class=\"ti ti-chevron-down\"></i>";
        }
        
        $html .= "</a>";

        if ($hasChildren) {
            $html .= "<ul class=\"{$options['dropdown_menu_class']}\">";
            foreach ($item->children as $child) {
                $html .= self::buildMenuItemHtml($child, $options);
            }
            $html .= "</ul>";
        }

        $html .= "</li>";
        
        return $html;
    }

    /**
     * Get URL for menu item based on link type
     */
    public static function getMenuItemUrl($item)
    {
        switch ($item->link_type) {
            case 'cms':
                return self::getCmsUrl($item->page_id);
            case 'service':
                return self::getServiceUrl($item->page_id);
            case 'blog':
                return self::getBlogUrl($item->page_id);
            case 'news':
                return self::getNewsUrl($item->page_id);
            case 'event':
                return self::getEventUrl($item->page_id);
            case 'category':
                return self::getCategoryUrl($item->page_id);
            case 'internal':
                return url($item->url);
            case 'external':
            case 'custom':
            default:
                return $item->url;
        }
    }

    /**
     * Get CMS page URL
     */
    private static function getCmsUrl($pageId)
    {
        $page = Cms::where('id', $pageId)->where('status', 1)->first();
        return $page ? url($page->slug) : '#';
    }

    /**
     * Get blog post URL
     */
    private static function getBlogUrl($blogId)
    {
        $blog = Blog::where('id', $blogId)->where('status', 1)->where('content_type', 'blog')->first();
        return $blog ? url('blog/' . $blog->slug) : '#';
    }

    /**
     * Get news article URL
     */
    private static function getNewsUrl($newsId)
    {
        $news = Blog::where('id', $newsId)->where('status', 1)->where('content_type', 'news')->first();
        return $news ? url('news/' . $news->slug) : '#';
    }

    /**
     * Get event URL
     */
    private static function getEventUrl($eventId)
    {
        $event = Blog::where('id', $eventId)->where('status', 1)->where('content_type', 'event')->first();
        return $event ? url('events/' . $event->slug) : '#';
    }

    /**
     * Get category URL
     */
    private static function getCategoryUrl($categoryId)
    {
        // You can implement category URL logic here
        return url('category/' . $categoryId);
    }

    private static function getServiceUrl($serviceId)
    {
        $service = Service::find($serviceId);
        if (!$service) return url('/services');
        return route('services.show', $service->slug);
    }

    

    /**
     * Get available pages for link types
     */
    public static function getAvailablePages($linkType)
    {
        switch ($linkType) {
            case 'cms':
                return Cms::where('status', 1)
                    ->select('id', 'title', 'slug')
                    ->orderBy('title')
                    ->get();
            case 'service':
                return Service::where('status', 1)
                    ->select('id', 'title', 'slug')
                    ->orderBy('sort_order')
                    ->get();
            case 'blog':
                return Blog::where('status', 1)
                    ->where('content_type', 'blog')
                    ->select('id', 'title', 'slug')
                    ->orderBy('title')
                    ->get();
            case 'news':
                return Blog::where('status', 1)
                    ->where('content_type', 'news')
                    ->select('id', 'title', 'slug')
                    ->orderBy('title')
                    ->get();
            case 'event':
                return Blog::where('status', 1)
                    ->where('content_type', 'event')
                    ->select('id', 'title', 'slug')
                    ->orderBy('title')
                    ->get();
            default:
                return collect();
        }
    }

    /**
     * Clear menu cache
     */
    public static function clearMenuCache($slug = null)
    {
        if ($slug) {
            Cache::forget("menu_{$slug}_1");
            Cache::forget("menu_{$slug}_0");
        } else {
            // Clear all menu caches
            Cache::flush();
        }
    }

    /**
     * Get breadcrumb from menu
     */
    public static function getBreadcrumb($menuSlug, $currentUrl)
    {
        $menuItems = self::getMenuForFrontend($menuSlug);
        return self::findBreadcrumbPath($menuItems, $currentUrl);
    }

    /**
     * Find breadcrumb path recursively
     */
    private static function findBreadcrumbPath($menuItems, $currentUrl, $path = [])
    {
        foreach ($menuItems as $item) {
            $itemUrl = self::getMenuItemUrl($item);
            $currentPath = array_merge($path, [$item]);
            
            if ($itemUrl === $currentUrl) {
                return $currentPath;
            }
            
            if ($item->children && $item->children->count() > 0) {
                $result = self::findBreadcrumbPath($item->children, $currentUrl, $currentPath);
                if ($result) {
                    return $result;
                }
            }
        }
        
        return null;
    }

    /**
     * Check if menu item is active
     */
    public static function isMenuItemActive($item, $currentUrl)
    {
        $itemUrl = self::getMenuItemUrl($item);
        return $itemUrl === $currentUrl;
    }

    /**
     * Get menu statistics
     */
    public static function getMenuStats($menuId)
    {
        $totalItems = MenuItem::where('menu_id', $menuId)->count();
        $activeItems = MenuItem::where('menu_id', $menuId)->where('status', 1)->count();
        $parentItems = MenuItem::where('menu_id', $menuId)->where('parent_id', 0)->count();
        $childItems = MenuItem::where('menu_id', $menuId)->where('parent_id', '>', 0)->count();

        return [
            'total' => $totalItems,
            'active' => $activeItems,
            'parents' => $parentItems,
            'children' => $childItems,
        ];
    }
}
