<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return Inertia::render('Blog/Index', [
            'posts' => Post::with('user')
                ->withCount('comments')
                ->latest()
                ->get(),
        ]);
    }

    public function show(Post $post)
    {
        return Inertia::render('Blog/Show', [
            'post' => $post->load(['user', 'comments.user']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $request->user()->posts()->create($request->only('title', 'description'));

        return back();
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $post->update($request->only('title', 'description'));

        return back();
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return redirect()->route('blog');
    }
}