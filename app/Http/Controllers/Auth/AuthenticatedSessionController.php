<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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

    /**
     * Get companies for a given email.
     */
    public function getCompanies(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Get the user based on email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'companies' => [],
                'message' => 'User not found',
                'usertype' => null,
                'company_name' => null
            ], 404);
        }

        // Get all companies (for the dropdown) if user is admin or superadmin
        $responseData = [
            'usertype' => $user->usertype,
            'company_name' => $user->company ? $user->company->name : null
        ];

        if (in_array($user->usertype, ['admin', 'superadmin'])) {
            $companies = Company::select('id', 'name')->get();
            $responseData['companies'] = $companies;
            $responseData['current_company_id'] = $user->company_id;
        } else {
            $responseData['companies'] = [];
        }

        return response()->json($responseData);
    }

    /**
     * Update the user's company.
     */
    public function updateCompany(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'company_id' => 'required|exists:companies,id'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        // Check if user is allowed to switch company
        if (!in_array($user->usertype, ['admin', 'superadmin'])) {
            return response()->json(['success' => false, 'message' => 'Only admin can switch company']);
        }

        // Update the company_id directly since it's stored in users table
        $user->company_id = $request->company_id;
        $user->save();

        return response()->json(['success' => true]);
    }
}
