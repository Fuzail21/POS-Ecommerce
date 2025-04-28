<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::paginate(20);
        $title = 'Customers List';
        return view('admin.customer.list', compact('customers', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Add New Customer';
        return view('admin.customer.add', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        try {
            $customer = new Customer();
            $customer->name = $validated['name'];
            $customer->contact = $validated['contact'];
            $customer->address = $validated['address'];
            $customer->save();

            return redirect()->route('customer.list')->with('success', 'Customer added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Edit Customer';
        $customer = Customer::find($id);
        return view('admin.customer.edit', compact('customer', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $customer = Customer::find($id);
            $customer->name = $request->name;
            $customer->contact = $request->contact;
            $customer->address = $request->address;
            $customer->save();

            return redirect()->route('customer.list')->with('success', 'Customer updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $customer->delete();
            return redirect()->route('customer.list')->with('success', 'Customer deleted successfully!');
        } else {
            return redirect()->route('customer.list')->with('error', 'Customer not found!');
        }
    }
}
