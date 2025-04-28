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
        $stores = Store::paginate(20);
        $title = 'Stores List';
        return view('admin.stores.list', compact('stores', 'title'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Add New Store';
        return view('admin.stores.add', compact('title'));
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
        ]);
    
        try {
            $store = new Store();
            $store->name = $request->name;
            $store->location = $request->location;
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
        return view('admin.stores.edit', compact('store', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $store = Store::find($id);
            $store->name = $request->name;
            $store->location = $request->location;
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
