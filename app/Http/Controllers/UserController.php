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
}
