<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $title = 'Warehouses List';
        $warehouses = Warehouse::paginate(20);
        return view('admin.warehouses.list', compact('warehouses', 'title'));
    }

    public function create()
    {
        $title = 'Add Warehouse';
        return view('admin.warehouses.form', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'capacity' => 'required|string|max:255',
        ]);

        $warehouse = new Warehouse();
        $warehouse->name = $request->name;
        $warehouse->location = $request->location;
        $warehouse->capacity = $request->capacity;
        // $warehouse->capacity_unit = $request->capacity_unit;
        $warehouse->save();

        return redirect()->route('warehouse.index')->with('success', 'Warehouse created successfully.');
    }


    public function edit($id)
    {
        $title = 'Edit Warehouse';
        $warehouse = Warehouse::findOrFail($id);
        return view('admin.warehouses.form', compact('warehouse', 'title'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'capacity' => 'required|string|max:255',
        ]);

        $warehouse = Warehouse::findOrFail($id);
        $warehouse->name = $request->name;
        $warehouse->location = $request->location;
        $warehouse->capacity = $request->capacity;
        // $warehouse->capacity_unit = $request->capacity_unit;
        $warehouse->save();

        return redirect()->route('warehouse.index')->with('success', 'Warehouse updated successfully.');
    }


    public function destroy($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->delete(); // soft delete

        return redirect()->route('warehouse.index')->with('success', 'Warehouse deleted successfully.');
    }
}

