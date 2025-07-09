<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash; // Import the Hash facade
use Illuminate\Validation\Rule;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $customers = Customer::paginate(20);
        $title = 'Customers List';
        return view('admin.customer.list', compact('customers', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $title = 'Add New Customer';
        return view('admin.customer.form', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255', // Add validation for last_name
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255', // Add validation for country
            'city' => 'nullable|string|max:255',     // Add validation for city
            'postcode' => 'nullable|string|max:255', // Add validation for postcode
            'balance' => 'nullable|numeric',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->last_name = $request->last_name; // Assign last_name
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->country = $request->country;   // Assign country
        $customer->city = $request->city;         // Assign city
        $customer->postcode = $request->postcode; // Assign postcode
        $customer->balance = $request->balance ?? 0;
        $customer->card_id = 'CUST-' . strtoupper(uniqid());
        $customer->password = Hash::make($request->password);
        $customer->save();

        return redirect()->route('customers.list')->with('success', 'Customer added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id){
        $title = 'Edit Customer';
        $customer = Customer::find($id);
        return view('admin.customer.form', compact('customer', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255', // Add validation for last_name
            'phone' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customer->id),
            ],
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255', // Add validation for country
            'city' => 'nullable|string|max:255',     // Add validation for city
            'postcode' => 'nullable|string|max:255', // Add validation for postcode
            'balance' => 'nullable|numeric',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $customer->name = $request->name;
        $customer->last_name = $request->last_name; // Update last_name
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->country = $request->country;   // Update country
        $customer->city = $request->city;         // Update city
        $customer->postcode = $request->postcode; // Update postcode
        $customer->balance = $request->balance ?? 0;

        if ($request->filled('password')) {
            $customer->password = Hash::make($request->password);
        }

        $customer->save();

        return redirect()->route('customers.list')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        Customer::findOrFail($id)->delete();
        return redirect()->route('customers.list')->with('success', 'Customer deleted successfully.');
    }

    public function showCard($id){
        $customer = Customer::findOrFail($id);
        return view('admin.customer.card', compact('customer'));
    }

}
