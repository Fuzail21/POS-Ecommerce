<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = Vendor::paginate(20);
        $title = 'Vendors List';
        return view('admin.vendor.list', compact('vendors', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Add New Vendor';
        return view('admin.vendor.add', compact('title'));
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
            // Create vendor manually
            $vendor = new Vendor();
            $vendor->name = $validated['name'];
            $vendor->contact = $validated['contact'];
            $vendor->address = $validated['address'];
            $vendor->save();

            return redirect()->route('vendor.list')->with('success', 'Vendor added successfully!');
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
        $title = 'Edit Vendor';
        $vendor = Vendor::find($id);
        return view('admin.vendor.edit', compact('vendor', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $vendor = Vendor::find($id);
            $vendor->name = $request->name;
            $vendor->contact = $request->contact;
            $vendor->address = $request->address;
            $vendor->save();

            return redirect()->route('vendor.list')->with('success', 'Vendor updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vendor = Vendor::find($id);
        if ($vendor) {
            $vendor->delete();
            return redirect()->route('vendor.list')->with('success', 'Vendor deleted successfully!');
        } else {
            return redirect()->route('vendor.list')->with('error', 'Vendor not found!');
        }
    }
}
