<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\User;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::with('manager')->paginate(20);

        $title = 'Stores List';
        return view('admin.stores.list', compact('stores', 'title'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $managers = User::where('role', 'Manager')->get();
        $title = 'Add New Store';
        return view('admin.stores.add', compact('title', 'managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'manager_id' => 'required|exists:users,id',
        ]);
    
        try {
            $store = new Store();
            $store->name = $request->name;
            $store->location = $request->location;
            $store->contact_number = $request->contact;
            $store->manager_id = $request->manager_id;
            $store->save();
        
            return redirect()->route('store.list')->with('success', 'Store added successfully!');
        } catch (\Exception $e) {
            return redirect()->route('store.list')->with('error', 'Something went wrong! Please try again.');
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Edit Store';
        $store = Store::find($id);
        $managers = User::where('role', 'Manager')->get();
        return view('admin.stores.edit', compact('store', 'title', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contact' => 'nullable|string|max:20',
            'manager_id' => 'nullable|exists:users,id',
        ]);
    
        try {
            $store = Store::findOrFail($id);
            $store->name = $request->name;
            $store->location = $request->location;
            $store->contact_number = $request->contact;
            $store->manager_id = $request->manager_id;
            $store->save();
    
            return redirect()->route('store.list')->with('success', 'Store updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('store.list')->with('error', 'Something went wrong! Please try again.');
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $store = Store::find($id);
        if ($store) {
            // Delete the store
            $store->delete();
    
            // Redirect back with a success message
            return redirect()->route('store.list')->with('success', 'Store deleted successfully!');
        } else {
            // Redirect back with an error message if store not found
            return redirect()->route('store.list')->with('error', 'Store not found!');
        }

    }
}
