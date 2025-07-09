<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerProfileController extends Controller
{
    /**
     * Display the customer's profile.
     */
    public function edit(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        return view('frontend.customer.profile.edit', compact('customer'));
    }

    /**
     * Update the customer's profile information.
     */
    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('customers')->ignore($customer->id)],
            // Add any other fields you want customers to be able to update
        ]);

        $customer->fill($request->validated());

        if ($customer->isDirty('email')) {
            $customer->email_verified_at = null; // If you have email verification for customers
        }

        $customer->save();

        return redirect()->route('customer.profile.edit')->with('status', 'Profile updated successfully.');
    }

    /**
     * Delete the customer's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password:customer'], // Specify the 'customer' guard
        ]);

        $customer = Auth::guard('customer')->user();

        Auth::guard('customer')->logout();

        $customer->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Your account has been deleted.');
    }
}