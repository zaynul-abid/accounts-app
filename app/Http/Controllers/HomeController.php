<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return view('frontend.pages.welcome.index');
    }

    public function dashboard(){
        if (Auth::check()) {
            return redirect('/admin/dashboard');
        }

        return redirect('/');
    }
}
