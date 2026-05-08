<?php

namespace App\Helpers;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FaqHelper
{
    public static function getFaqsByCategory($categorySlug, $limit = null)
    {
        $cacheKey = "faqs_category_{$categorySlug}";
        
        return Cache::remember($cacheKey, 3600, function () use ($categorySlug, $limit) {
            $query = Faq::with('category')
                ->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug)->where('status', 1);
                })
                ->where('status', 1)
                ->orderBy('sort_order', 'asc');
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    public static function getFaqsByCategoryId($categoryId, $limit = null)
    {
        $cacheKey = "faqs_category_id_{$categoryId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($categoryId, $limit) {
            $query = Faq::with('category')
                ->where('category_id', $categoryId)
                ->where('status', 1)
                ->orderBy('sort_order', 'asc');
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    public static function getActiveCategories()
    {
        return Cache::remember('faq_categories_active', 3600, function () {
            return FaqCategory::where('status', 1)
                ->orderBy('sort_order', 'asc')
                ->get();
        });
    }

    public static function getAllFaqs()
    {
        return Cache::remember('faqs_all', 3600, function () {
            return Faq::with('category')
                ->where('status', 1)
                ->orderBy('sort_order', 'asc')
                ->get();
        });
    }

    public static function getFaqsByPage($pageType, $pageId, $limit = null)
    {
        $bust = Cache::get('faq_assignments_cache_bust', 'v1');
        $cacheKey = "faqs_page_{$pageType}_{$pageId}_{$bust}";

        return Cache::remember($cacheKey, 3600, function () use ($pageType, $pageId, $limit) {
            $query = Faq::where('page_type', $pageType)
                ->whereIn('page_id', [$pageId, 0])
                ->where('status', 1)
                ->orderBy('sort_order', 'asc');

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get();
        });
    }

    public static function clearCache()
    {
        Cache::forget('faqs_all');
        Cache::forget('faq_categories_active');
        
        $categories = FaqCategory::all();
        foreach ($categories as $category) {
            Cache::forget("faqs_category_{$category->slug}");
            Cache::forget("faqs_category_id_{$category->id}");
        }
        Cache::forever('faq_assignments_cache_bust', Str::random(8));
    }

    public static function getFaqCountByCategory($categorySlug)
    {
        return Cache::remember("faq_count_{$categorySlug}", 3600, function () use ($categorySlug) {
            return Faq::whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug)->where('status', 1);
            })->where('status', 1)->count();
        });
    }
}
















