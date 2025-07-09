<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\Customer;
use Session;


class CheckoutController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;
        $user = Customer::find($userId);
        $cart = session()->get('cart', []);
        return view('store.checkout', compact('cart', 'user')); // Create this view
    }

    public function process(Request $request)
    {
        // Logic to process the order
        // Get the authenticated customer using Auth::guard('customer')->user()
        $customer = Auth::guard('customer')->user();
        // Process payment, create order, clear cart, etc.

        return redirect()->route('store.order.success')->with('success', 'Order placed successfully!'); // Redirect to an order success page
    }
}