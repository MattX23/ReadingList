<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    public function home()
    {
        if (Auth::user()) {
            return View::make('home');
        }
        return View::make('auth.login');
    }
}
