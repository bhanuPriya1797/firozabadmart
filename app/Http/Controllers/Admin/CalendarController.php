<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalendarController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index()
    {
        $items = CalendarEvent::ordered()->paginate(20);
        return view('admin.calendar.index', compact('items'));
    }

    public function create()
    {
        $item = new CalendarEvent();
        return view('admin.calendar.form', compact('item'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        if (empty($data['slug'])) {
            $data['slug'] = CalendarEvent::generateSlug($data['title']);
        }
        CalendarEvent::create($data);
        return redirect()->route($this->ADMIN_ROUTE_NAME.'.calendar.index')->with('success', 'Calendar event created.');
    }

    public function edit($id)
    {
        $item = CalendarEvent::findOrFail($id);
        return view('admin.calendar.form', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = CalendarEvent::findOrFail($id);
        $data = $this->validateData($request);
        if (!empty($data['slug']) && $data['slug'] !== $item->slug) {
            $data['slug'] = CalendarEvent::generateSlug($data['slug']);
        }
        $item->update($data);
        return redirect()->route($this->ADMIN_ROUTE_NAME.'.calendar.index')->with('success', 'Calendar event updated.');
    }

    public function destroy($id)
    {
        $item = CalendarEvent::findOrFail($id);
        $item->delete();
        return back()->with('success', 'Calendar event deleted.');
    }

    protected function validateData(Request $request): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'external_url' => 'nullable|url|max:500',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
        $v = Validator::make($request->all(), $rules);
        $v->validate();
        $data = $v->validated();
        $data['featured'] = $request->boolean('featured');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        return $data;
    }
}
