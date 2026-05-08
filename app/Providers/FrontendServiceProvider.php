<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Cms;
use App\Models\Blog;
use App\Helpers\CustomHelper;

class FrontendServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share SEO data with all frontend views
        View::composer('components.frontend.layout', function ($view) {
            $seoData = $this->getDynamicSeoData();
            $view->with('seoData', $seoData);
        });
    }
    
    /**
     * Get dynamic SEO data based on current route and page
     *
     * @return array
     */
    private function getDynamicSeoData()
    {
        $request = request();
        $currentUrl = $request->path();
        
        // Debug logging
        \Log::info('FrontendServiceProvider - Current URL: ' . $currentUrl);
        
        // Default SEO data
        $seoData = [
            'title' => config('app.name', 'IL Mission'),
            'description' => 'IL Mission supports meritorious students in Kashmir to achieve their educational dreams through financial aid and mentorship.',
            'keywords' => 'education, scholarship, Kashmir, students, financial aid, mentorship',
        ];
        
        // Get settings for fallback
        $settings = CustomHelper::getSettings(['meta_title', 'meta_description', 'meta_keywords']);
        
        // Handle CMS pages
        if ($currentUrl && $currentUrl !== '/' && !str_starts_with($currentUrl, 'news/')) {
            $cmsPage = Cms::where('slug', $currentUrl)
                ->where('status', 1)
                ->first();
                
            if ($cmsPage && !empty($cmsPage->seo)) {
                $pageSeo = is_string($cmsPage->seo) ? json_decode($cmsPage->seo, true) : $cmsPage->seo;
                
                if (is_array($pageSeo)) {
                    // Handle both old format (title, description, keywords) and new format (meta_title, meta_description, meta_keywords)
                    if (!empty($pageSeo['meta_title'])) {
                        $seoData['title'] = $pageSeo['meta_title'];
                    } elseif (!empty($pageSeo['title'])) {
                        $seoData['title'] = $pageSeo['title'];
                    }
                    
                    if (!empty($pageSeo['meta_description'])) {
                        $seoData['description'] = $pageSeo['meta_description'];
                    } elseif (!empty($pageSeo['description'])) {
                        $seoData['description'] = $pageSeo['description'];
                    }
                    
                    if (!empty($pageSeo['meta_keywords'])) {
                        $seoData['keywords'] = $pageSeo['meta_keywords'];
                    } elseif (!empty($pageSeo['keywords'])) {
                        $seoData['keywords'] = $pageSeo['keywords'];
                    }
                }
                
                // If page has a title but no SEO title, use page title
                if (empty($seoData['title']) && !empty($cmsPage->title)) {
                    $seoData['title'] = $cmsPage->title;
                }
            }
        }
        
        // Handle news detail pages
        if (str_starts_with($currentUrl, 'news/') && $currentUrl !== 'news') {
            $newsSlug = str_replace('news/', '', $currentUrl);
            $newsArticle = Blog::where('slug', $newsSlug)
                ->where('content_type', 'news')
                ->where('status', 1)
                ->first();
                
            if ($newsArticle) {
                $seoData['title'] = $newsArticle->meta_title ?: ($newsArticle->title . ' - ' . config('app.name', 'IL Mission'));
                $seoData['description'] = $newsArticle->meta_description ?: ($newsArticle->brief ?: substr(strip_tags($newsArticle->content), 0, 160));
                $seoData['keywords'] = $newsArticle->meta_keyword ?: 'news, IL Mission, Kashmir, education';
            }
        }
        
        // Handle news listing page
        if ($currentUrl === 'news') {
            $seoData['title'] = 'Latest News - ' . config('app.name', 'IL Mission');
            $seoData['description'] = 'Stay updated with the latest news, events, and updates from IL Mission. Read about our initiatives, student success stories, and community impact.';
            $seoData['keywords'] = 'news, updates, IL Mission, Kashmir, education, student stories, community impact';
        }
        
        // Handle homepage
        if ($currentUrl === '/' || $currentUrl === '') {
            if (!empty($settings['meta_title'])) {
                $seoData['title'] = $settings['meta_title'];
            }
            if (!empty($settings['meta_description'])) {
                $seoData['description'] = $settings['meta_description'];
            }
            if (!empty($settings['meta_keywords'])) {
                $seoData['keywords'] = $settings['meta_keywords'];
            }
        }
        
        // Fallback to settings if no specific SEO data found
        if ($seoData['title'] === config('app.name', 'IL Mission')) {
            if (!empty($settings['meta_title'])) {
                $seoData['title'] = $settings['meta_title'];
            }
            if (!empty($settings['meta_description'])) {
                $seoData['description'] = $settings['meta_description'];
            }
            if (!empty($settings['meta_keywords'])) {
                $seoData['keywords'] = $settings['meta_keywords'];
            }
        }
        
        // Debug logging
        \Log::info('FrontendServiceProvider - Final SEO Data:', $seoData);
        
        return $seoData;
    }
}
