<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use App\Models\Blog;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BlogCommentController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $comments = BlogComment::with('blog')->select('id', 'blog_id', 'name', 'email', 'website', 'comment', 'status', 'is_read', 'created_at');

            if ($request->filled('status')) {
                $comments->where('status', (int)$request->status);
            }
            if ($request->filled('blog_id')) {
                $comments->where('blog_id', (int)$request->blog_id);
            }

            return DataTables::of($comments)
                ->addColumn('blog_title', function ($row) {
                    return optional($row->blog)->title ?: '-';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y, h:i A') : '';
                })
                ->addColumn('status_label', function ($row) {
                    return $row->status
                        ? '<span class="badge bg-label-primary">Approved</span>'
                        : '<span class="badge bg-label-secondary">Pending</span>';
                })
                ->addColumn('action', function ($row) {
                    $toggleUrl = route($this->ADMIN_ROUTE_NAME . '.blog-comments.update-status');
                    $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.blog-comments.delete', $row->id);
                    $markUrl = route($this->ADMIN_ROUTE_NAME . '.blog-comments.mark_read');
                    $approveBtn = '<button type="button" class="btn btn-label-success btn-sm btn-toggle-status" data-url="' . $toggleUrl . '" data-id="' . $row->id . '" data-status="1"><i class="ti tabler-check"></i></button>';
                    $pendingBtn = '<button type="button" class="btn btn-label-warning btn-sm btn-toggle-status" data-url="' . $toggleUrl . '" data-id="' . $row->id . '" data-status="0"><i class="ti tabler-eye-off"></i></button>';
                    $markBtn = '<button type="button" class="btn btn-label-info btn-sm btn-mark-read" data-url="' . $markUrl . '" data-id="' . $row->id . '" data-read="' . ($row->is_read ? 0 : 1) . '"><i class="ti ' . ($row->is_read ? 'tabler-eye-off' : 'tabler-eye') . '"></i></button>';
                    $deleteBtn = '<button type="button" class="btn btn-label-danger btn-sm btn-delete-comment" data-url="' . $deleteUrl . '"><i class="ti tabler-trash"></i></button>';
                    return '<div class="btn-group" role="group">' . $approveBtn . $pendingBtn . $markBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['status_label', 'action'])
                ->make(true);
        }

        $blogs = Blog::where('content_type', 'blog')->orderBy('title')->select('id', 'title')->get();
        return view('admin.blog-comments.index', compact('blogs'));
    }

    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'status' => 'required|in:0,1',
        ]);
        $comment = BlogComment::findOrFail($data['id']);
        $comment->status = (int)$data['status'];
        $comment->save();

        CustomHelper::recordActionLog(
            url()->current(),
            'blog_comments',
            $comment->id,
            'Update Comment Status',
            'Updated status to ' . ($comment->status ? 'Approved' : 'Pending'),
            json_encode($data)
        );

        return response()->json(['status' => true]);
    }

    public function delete(Request $request, $id)
    {
        $comment = BlogComment::findOrFail((int)$id);
        $comment->delete();

        CustomHelper::recordActionLog(
            url()->current(),
            'blog_comments',
            $id,
            'Delete Comment',
            'Deleted comment by ' . ($comment->name ?? ''),
            ''
        );

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        return redirect()->route($this->ADMIN_ROUTE_NAME . '.blog-comments.index')->with('success', 'Comment deleted successfully');
    }

    public function markRead(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'read' => 'nullable|in:0,1',
        ]);
        $comment = BlogComment::findOrFail($data['id']);
        $comment->is_read = (int)($data['read'] ?? 1);
        $comment->save();
        CustomHelper::recordActionLog(
            url()->current(),
            'blog_comments',
            $comment->id,
            'Mark Comment Read',
            'Marked as ' . ($comment->is_read ? 'Read' : 'Unread'),
            json_encode($data)
        );
        return response()->json(['status' => true]);
    }
}
