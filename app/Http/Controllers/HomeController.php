<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $companies = Company::all();
        return view('frontend.pages.Welcome.index', compact('companies'));
    }
    public function redirectToLogin(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        // Store company_id in session
        session(['selected_company_id' => $request->company_id]);

        return redirect()->route('login');
    }
}
