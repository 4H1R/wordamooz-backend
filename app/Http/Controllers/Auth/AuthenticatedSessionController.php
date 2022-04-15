<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\AuthUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Auth
 *
 * Api for managing categories
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Get current authenticated user.
     * @authenticated
     */
    public function index(Request $request)
    {
        return new AuthUserResource($request->user());
    }

    /**
     * Login the user.
     * @unauthenticated
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return response()->noContent();
    }

    /**
     * Logout the user.
     * @authenticated
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
