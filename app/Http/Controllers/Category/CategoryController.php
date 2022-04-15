<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * @group Category
 *
 * Api for managing categories
 */
class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of the public resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories =  Category::query()
            ->where('is_public', true)
            ->cursorPaginate();

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     * @authenticated
     */
    public function store(Request $request)
    {
        $this->authorize('create', Category::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'required|boolean',
        ]);

        $category = Category::create([...$validated, 'user_id' => $request->user()->id]);

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $this->authorize('view', $category);

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     * @authenticated
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'required|boolean',
        ]);

        $category->update($validated);
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     * @authenticated
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
