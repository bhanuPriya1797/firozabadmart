<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;

class TeamMemberController extends Controller
{
    public function index()
    {
        $items = TeamMember::ordered()->paginate(20);
        return view('admin.team_members.index', compact('items'));
    }

    public function create()
    {
        $item = new TeamMember();
        return view('admin.team_members.form', compact('item'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        TeamMember::create($data);
        return redirect()->route(CustomHelper::getAdminRouteName().'.team-members.index')->with('success', 'Team member created.');
    }

    public function edit($id)
    {
        $item = TeamMember::findOrFail($id);
        return view('admin.team_members.form', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = TeamMember::findOrFail($id);
        $data = $this->validateData($request);
        $item->update($data);
        return redirect()->route(CustomHelper::getAdminRouteName().'.team-members.index')->with('success', 'Team member updated.');
    }

    public function destroy($id)
    {
        $item = TeamMember::findOrFail($id);
        $item->delete();
        return back()->with('success', 'Team member deleted.');
    }

    protected function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);
        $validated['featured'] = $request->boolean('featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        return $validated;
    }
}
