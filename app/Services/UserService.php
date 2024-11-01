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
}