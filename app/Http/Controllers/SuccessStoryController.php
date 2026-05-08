<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuccessStory;

class SuccessStoryController extends Controller
{
    /**
     * Display a listing of success stories
     */
    public function index()
    {
        $stories = SuccessStory::active()
            ->ordered()
            ->paginate(12);
            
        return view('frontend.pages.success-stories', compact('stories'));
    }

    /**
     * Display the specified success story
     */
    public function show($id)
    {
        $decryptedId = SuccessStory::decryptId($id);
        $story = SuccessStory::active()->findOrFail($decryptedId);
        
        // Get related stories (same category or random)
        $relatedStories = SuccessStory::active()
            ->where('id', '!=', $decryptedId)
            ->take(3)
            ->get();
            
        return view('frontend.pages.success-story-detail', compact('story', 'relatedStories'));
    }
}
