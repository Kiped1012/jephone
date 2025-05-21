<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = collect(include resource_path('data/user.php'));
        $title = 'Manajemen User';

        return view('manageuser', compact('users', 'title'));
    }
}
