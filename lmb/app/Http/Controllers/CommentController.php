<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
class CommentController extends Controller
{
    public function store(Request $request, $loan_id)
    {
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        Comment::create([
            'loan_id' => $loan_id,
            'comment' => $request->comment,
            'user_id' => Auth::id(), // Assumes user is logged in
            'created_at' => now(), // Optional, if not using timestamps in migration
        ]);

        return back()->with('success', 'Comment added successfully.');
    }
}
