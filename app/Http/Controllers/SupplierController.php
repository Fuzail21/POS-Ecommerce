<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(){
        $title = 'Suppliers List';
        $suppliers = Supplier::paginate(20);
        return view('admin.supplier.list', compact('suppliers', 'title'));
    }

    public function create(){
        $title = 'Add Supplier';
        return view('admin.supplier.form', compact('title'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'balance' => 'nullable|numeric',
        ]);

        $supplier = new Supplier();
        $supplier->name = $validated['name'];
        $supplier->contact_person = $validated['contact_person'] ?? null;
        $supplier->phone = $validated['phone'] ?? null;
        $supplier->email = $validated['email'] ?? null;
        $supplier->address = $validated['address'] ?? null;
        $supplier->balance = $validated['balance'] ?? 0;
        $supplier->save();

        return redirect()->route('suppliers.list')->with('success', 'Supplier added successfully.');
    }

    public function edit($id){
        $title = 'Edit Supplier';
        $supplier = Supplier::findOrFail($id);
        return view('admin.supplier.form', compact('supplier', 'title'));
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'balance' => 'nullable|numeric',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->name = $validated['name'];
        $supplier->contact_person = $validated['contact_person'] ?? null;
        $supplier->phone = $validated['phone'] ?? null;
        $supplier->email = $validated['email'] ?? null;
        $supplier->address = $validated['address'] ?? null;
        $supplier->balance = $validated['balance'] ?? 0;
        $supplier->save();

        return redirect()->route('suppliers.list')->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id){
        Supplier::findOrFail($id)->delete();
        return redirect()->route('suppliers.list')->with('success', 'Supplier deleted successfully.');
    }
}

