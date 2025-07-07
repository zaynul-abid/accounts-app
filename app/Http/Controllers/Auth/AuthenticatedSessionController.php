<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Assign company_id from session to the authenticated user
        $user = Auth::user();
        if ($request->session()->has('selected_company_id')) {
            $companyId = $request->session()->get('selected_company_id');
            $user->company()->associate(Company::find($companyId));
            $user->save();
            $request->session()->forget('selected_company_id'); // Clean up session
        }

        // Role-based redirection
        if ($user->isSuperAdmin()) {
            return redirect()->intended('/superadmin/dashboard');
        } elseif ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->isEmployee()) {
            return redirect()->intended('/employee/dashboard');
        }

        return redirect('/');
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
