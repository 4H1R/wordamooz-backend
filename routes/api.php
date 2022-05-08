<?php

use App\Http\Controllers\Category\UserCategoryController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('categories', CategoryController::class);
Route::apiResource('users.categories', UserCategoryController::class)
    ->scoped()
    ->only('index');
Route::apiResource('categories.posts', PostController::class)->scoped();
