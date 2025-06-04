<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(){
        $title = 'Branches List';
        $branches = Branch::with('warehouse')->paginate(20);
        return view('admin.branch.list', compact('branches', 'title'));
    }

    public function create(){
        $title = 'Add Branch';
        $warehouses = Warehouse::all();
        return view('admin.branch.form', compact('warehouses', 'title'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'contact' => 'required',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        try {
            $branch = new Branch();
            $branch->name = $request->name;
            $branch->location = $request->location;
            $branch->contact = $request->contact;
            $branch->warehouse_id = $request->warehouse_id;
            $branch->save();

            return redirect()->route('branch.list')->with('success', 'Branch created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create branch. Please try again.');
        }
    }

    public function edit($id){
        $title = 'Edit Branch';
        $branch = Branch::findOrFail($id);
        $warehouses = Warehouse::all();
        return view('admin.branch.form', compact('branch', 'warehouses', 'title'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'contact' => 'required',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        try {
            $branch = Branch::findOrFail($id);
            $branch->name = $request->name;
            $branch->location = $request->location;
            $branch->contact = $request->contact;
            $branch->warehouse_id = $request->warehouse_id;
            $branch->save();

            return redirect()->route('branch.list')->with('success', 'Branch updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update branch. Please try again.');
        }
    }

    public function destroy($id){
        Branch::findOrFail($id)->delete();
        return redirect()->route('branch.list')->with('success', 'Branch deleted successfully.');
    }
}

