<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getUsersExcept($id)
    {
        $users = User::whereNot('id', $id)->get(['id','name'])->toArray();
        return $users;
    }

    public function getUsers($filters = [])
    {
        // dd($filters);
        $users = User::when(isset($filters['search']),function($q) use($filters){
            $q->where('name','LIKE','%'.$filters['search'].'%')
                ->orWhere('email','LIKE','%'.$filters['search'].'%');

        })->limit(10)->get();

        return response()->json($users);
    }
}