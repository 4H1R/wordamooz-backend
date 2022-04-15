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
     * Display a listing of the user resource.
     * @authenticated
     */
    public function index(User $user)
    {
        $this->authorize('viewAny', [Category::class, $user]);

        $categories = $user->categories()->cursorPaginate();
        return CategoryResource::collection($categories);
    }
}
