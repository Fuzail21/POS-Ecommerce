<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerProfileController extends Controller
{
    public function edit(Request $request){
        $customer = Auth::guard('customer')->user();
        return view('store.profile', compact('customer'));
    }

    public function update(Request $request){
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],    
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('customers')->ignore($customer->id)],
            'address' => ['nullable', 'string', 'max:500'],   
            'country' => ['nullable', 'string', 'max:255'],   
            'city' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:255'],  
        ]);

        $customer->fill($data);

        if ($customer->isDirty('email')) {
            $customer->email_verified_at = null; 
        }

        $customer->save();

        return redirect()->route('customer.profile.edit')->with('status', 'Profile updated successfully.');
    }


    /**
     * Delete the customer's account.
     */
    public function destroy(Request $request){
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