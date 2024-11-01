<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::whereNot('id', auth()->id())->get();

        return Inertia::render('Chat/EmptyChat',[
            'users' => $users
        ]);

    }
    
}
