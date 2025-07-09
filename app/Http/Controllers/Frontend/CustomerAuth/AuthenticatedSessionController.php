<?php

namespace App\Http\Controllers\Frontend\CustomerAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomerLoginRequest; // Create this request
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
        return view('store.auth.login'); // Create this view
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(CustomerLoginRequest $request): RedirectResponse
    {
        $request->authenticate('customer'); // Specify the 'customer' guard

        $request->session()->regenerate();

        // Redirect to checkout or a customer dashboard
        return redirect()->intended(route('store.checkout', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout(); // Specify the 'customer' guard

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/store'); // Redirect to the store homepage
    }
}