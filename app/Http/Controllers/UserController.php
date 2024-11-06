<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request,UserService $userService)
    {
        $users = $userService->getUsers($request->all());

        return $users;
    }

    public function updateLastActiveAt(Request $request)
    {

        auth()->user()->update([
            'last_active_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Updated Last Active At."
        ]);
    }
}
