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
     * Display a listing of the resource.
     */
    public function index(Category $category)
    {
        $this->authorize('viewAny', [Post::class, $category]);

        $posts = $category->posts()->cursorPaginate();
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(Category $category, Post $post)
    {
        $this->authorize('view', [$post, $category]);

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     * @authenticated
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
