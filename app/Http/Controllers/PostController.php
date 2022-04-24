<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

/**
 * @group Post
 *
 * Api for managing posts
 */
class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Show category posts.
     */
    public function index(Category $category)
    {
        $this->authorize('viewAny', [Post::class, $category]);

        $posts = $category->posts()->cursorPaginate();
        return PostResource::collection($posts);
    }

    /**
     * Create a new post.
     * @authenticated
     */
    public function store(Request $request, Category $category)
    {
        $this->authorize('create', [Post::class, $category]);

        $validated = $request->validate([
            'word' => 'required|string|max:255',
            'meaning' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post = $category->posts()->create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);
        return (new PostResource($post))->response()->setStatusCode(201);
    }

    /**
     * Show a post.
     */
    public function show(Category $category, Post $post)
    {
        $this->authorize('view', [$post, $category]);

        return new PostResource($post);
    }

    /**
     * Update a post.
     * @authenticated
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Delete a post.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
