<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Category
 */
class UserCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Show user categories.
     * @bodyParam query string optional Search query
     * @authenticated
     */
    public function index(User $user)
    {
        $this->authorize('viewAny', [Category::class, $user]);

        $categories = $user->categories()
            ->search()
            ->latest('id')
            ->cursorPaginate();

        return CategoryResource::collection($categories);
    }
}
