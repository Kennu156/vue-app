<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $post->comments()->create([
            'body'    => $request->body,
            'user_id' => $request->user()->id,
        ]);

        return back();
    }

    public function destroy(Comment $comment)
    {
        $user = auth()->user();

        if ($user->id !== $comment->user_id && !$user->is_admin) {
            abort(403);
        }

        $comment->delete();
        return back();
    }
}