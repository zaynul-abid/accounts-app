<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Role-based redirection
            if ($user->isSuperAdmin()) {
                return redirect()->intended('/superadmin/dashboard');
            } elseif ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->isEmployee()) {
                return redirect()->intended('/employee/dashboard');
            }

            // Default fallback redirect if role not matched
            return redirect()->route('dashboard');
        }

        // If user is not authenticated, show welcome page
        $companies = Company::all();
        return view('frontend.pages.welcome.index', compact('companies'));
    }
}
