<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    public function login()
    {
        if (Auth::user()) {
            return View::make('home');
        }
        return View::make('auth.login');
    }
}
