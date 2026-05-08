<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Enquiry;
use App\Models\Partner;
use App\Helpers\CustomHelper;
use App\Models\Country; // Added this import for countries
use App\Models\Cms; // Added this import for CMS pages
use App\Helpers\CustomFieldHelper; // Added this import for custom fields
use App\Models\NewsletterSubscriber;
use App\Models\SuccessStory;
use App\Helpers\FaqHelper;
use App\Models\Service;
use App\Models\GalleryFolder;
use App\Models\GalleryImage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\ContactEnquiryMail;
use App\Models\Circular;
use App\Models\Achievement;
use App\Models\Archer;
use App\Models\TeamMember;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    /**
     * Display the homepage
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch Homepage Slider
        $banners = Banner::where('slug', 'homepage-slider')
            ->active()
            ->with(['images' => function($q) { $q->orderBy('sort_order', 'asc'); }])
            ->first();

        // Fetch Latest News
        $news = Blog::where('content_type', 'news')
            ->where('status', 1)
            ->orderBy('blog_date', 'desc')
            ->take(5)
            ->get();

        // Fetch Upcoming Events
        $events = Blog::where('content_type', 'event')
            ->where('status', 1)
            ->where('blog_date', '>=', date('Y-m-d'))
            ->orderBy('blog_date', 'asc')
            ->take(6)
            ->get();

        // Fetch Partners
        $partners = Partner::where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->get();

        // Fetch Featured Coach / Team Member for homepage section
        $coach = TeamMember::active()
            ->orderByDesc('featured')
            ->ordered()
            ->first();

        // Fetch Featured Circulars
        $circulars = Circular::where('status', 1)
            ->where('featured', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->take(3)
            ->get(['title','slug','document_path']);

        $seoData = $this->getSeoData('home', 'Home');

        return view('frontend.home', compact('banners', 'news', 'events', 'partners', 'circulars', 'seoData', 'coach'));
    }

    // ... (rest of the controller methods)

    public function circulars(Request $request)
    {
        $items = Circular::where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(20, ['id','title','slug','document_path']);
        $seoData = $this->getSeoData('circulars', 'Circulars');
        return view('frontend.pages.circulars', compact('items', 'seoData'));
    }

    public function achievements(Request $request)
    {
        $query = Achievement::where('status', 1);
        if ($request->filled('medal')) {
            $query->where('medal', $request->medal);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('q')) {
            $s = $request->q;
            $query->where(function ($q2) use ($s) {
                $q2->where('winner_name', 'like', "%{$s}%")
                   ->orWhere('event_name', 'like', "%{$s}%")
                   ->orWhere('location', 'like', "%{$s}%");
            });
        }
        $items = $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc')->paginate(24);
        $seoData = $this->getSeoData('achievements', 'Achievements');
        return view('frontend.pages.achievements', compact('items', 'seoData'));
    }

    public function archers(Request $request)
    {
        $appsSub = DB::table('archer_applications as aa')
            ->select('aa.archer_id', 'aa.status')
            ->join(DB::raw('(SELECT archer_id, MAX(id) AS last_id FROM archer_applications GROUP BY archer_id) AS t'), 'aa.id', '=', 't.last_id');

        $query = Archer::query()
            ->leftJoinSub($appsSub, 'apps', function ($join) {
                $join->on('apps.archer_id', '=', 'archers.id');
            })
            ->where('archers.status', 1)
            ->where('apps.status', 'approved');

        if ($request->filled('q')) {
            $s = $request->q;
            $query->where(function ($q2) use ($s) {
                $q2->where('first_name', 'like', "%{$s}%")
                   ->orWhere('surname', 'like', "%{$s}%")
                   ->orWhere('email', 'like', "%{$s}%")
                   ->orWhere('phone', 'like', "%{$s}%");
            });
        }
        if ($request->filled('category')) {
            $query->where('category', 'like', '%' . $request->category . '%');
        }

        $items = $query->orderBy('archers.first_name')->orderBy('archers.surname')->paginate(24, ['archers.*']);
        $seoData = $this->getSeoData('archers', 'Archers');
        return view('frontend.pages.archers', compact('items', 'seoData'));
    }

    private function getSeoData($page, $defaultTitle) {
        // Simple helper or just return array
        return [
            'meta_title' => $defaultTitle,
            'meta_keyword' => '',
            'meta_description' => ''
        ];
    }
    
    // Keeping other methods as placeholders or valid if used elsewhere
    // public function about() { return view('frontend.pages.about'); }
    
    public function showNews($slug)
    {
        $news = Blog::where('content_type', 'news')
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $seoData = $this->getSeoData('news', $news->title);

        return view('frontend.pages.news_detail', compact('news', 'seoData'));
    }

    public function news()
    {
        $news = Blog::where('content_type', 'news')
            ->where('status', 1)
            ->orderBy('blog_date', 'desc')
            ->paginate(12);

        $seoData = $this->getSeoData('news', 'Latest News');

        return view('frontend.pages.news', compact('news', 'seoData'));
    }

    public function events()
    {
        $events = Blog::where('content_type', 'event')
            ->where('status', 1)
            ->orderBy('blog_date', 'desc')
            ->paginate(12);

        $seoData = $this->getSeoData('events', 'Events');

        return view('frontend.pages.events', compact('events', 'seoData'));
    }

    public function showEvent($slug)
    {
        $event = Blog::where('content_type', 'event')
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $seoData = $this->getSeoData('events', $event->title);

        return view('frontend.pages.event_detail', compact('event', 'seoData'));
    }

    public function calendar(Request $request)
    {
        $items = \App\Models\CalendarEvent::active()
            ->ordered()
            ->get(['title','start_date','end_date','location','category','slug','external_url','description']);

        $grouped = [];
        foreach ($items as $ev) {
            $monthKey = $ev->start_date ? $ev->start_date->format('F Y') : 'TBA';
            if (!isset($grouped[$monthKey])) $grouped[$monthKey] = [];
            $grouped[$monthKey][] = $ev;
        }
        $seoData = $this->getSeoData('calendar', 'Calendar');
        return view('frontend.pages.calendar', compact('grouped', 'seoData'));
    }

    // public function contact() { return view('frontend.pages.contact'); }
    // public function blogs(Request $request) { return view('frontend.pages.blogs'); }
    // public function blogDetail($slug) { return view('frontend.pages.blog_detail'); }

    public function gallery()
    {
        // Fetch CMS page for title/metadata if it exists
        $page = Cms::where('slug', 'gallery')->active()->first();
        
        $folders = GalleryFolder::active()->ordered()->get();
        
        // If CMS page exists, use its SEO data, otherwise default
        $seoData = $page 
            ? $this->getSeoData('cms', $page->title)
            : $this->getSeoData('gallery', 'Gallery');

        return view('frontend.pages.gallery', compact('page', 'folders', 'seoData'));
    }

    public function galleryDetail($slug)
    {
        $folder = GalleryFolder::where('slug', $slug)->active()->firstOrFail();
        $images = $folder->images()->active()->ordered()->get();
        
        $seoData = $this->getSeoData('gallery', $folder->name);

        return view('frontend.pages.gallery_detail', compact('folder', 'images', 'seoData'));
    }

    public function cms($slug)
    {
        $page = Cms::where('slug', $slug)->active()->firstOrFail();
        $seoData = $this->getSeoData('cms', $page->title);

        // Map slugs to specific views if they exist
        $viewName = $slug;
        if ($slug === 'contact-us') {
            $viewName = 'contact';
        } elseif ($slug === 'about-us') {
            $viewName = 'about';
        }

        if (view()->exists('frontend.pages.' . $viewName)) {
            if ($viewName === 'about') {
                $partners = Partner::where('status', 1)->orderBy('sort_order', 'asc')->get();
                return view('frontend.pages.' . $viewName, compact('page', 'seoData', 'partners'));
            }
            return view('frontend.pages.' . $viewName, compact('page', 'seoData'));
        }

        return view('frontend.pages.cms.default', compact('page', 'seoData'));
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $comment = $request->message;
        if ($request->subject) {
            $comment = "Subject: " . $request->subject . "\n\n" . $comment;
        }

        Enquiry::create([
            'name' => $request->name,
            'contact_email' => $request->email,
            'phone' => $request->phone,
            'comment' => $comment,
            'ip_address' => $request->ip(),
            'is_read' => 0,
        ]);

        return redirect()->back()->with('success', 'Thank you for contacting us. We will get back to you soon.');
    }

    public function subscribeNewsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower($validated['email']);
        $existing = NewsletterSubscriber::where('email', $email)->first();

        if ($existing) {
            if (isset($existing->status) && (int) $existing->status !== 1) {
                $existing->status = 1;
                $existing->save();
            }
            $message = 'You are already subscribed.';
        } else {
            NewsletterSubscriber::create([
                'email' => $email,
                'status' => 1,
            ]);
            $message = 'Thank you for subscribing!';
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
