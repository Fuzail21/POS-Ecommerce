<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(){
        $title = 'Units List';
        $units = Unit::latest()->paginate(10);
        return view('admin.units.list', compact('units', 'title'));
    }

    public function create(){
        $title = 'Add Unit';
        $data = compact('title');
        return view('admin.units.form', ['unit' => new Unit()])->with($data);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'base_unit' => 'nullable|string|max:255',
            'conversion_factor' => 'required|numeric|min:0',
        ]);

        $unit = new Unit();
        $unit->name = $request->name;
        $unit->base_unit = $request->base_unit;
        $unit->conversion_factor = $request->conversion_factor;
        $unit->save();

        return redirect()->route('units.list')->with('success', 'Unit added successfully.');
    }

    public function edit($id){
        $title = 'Edit Unit';
        $unit = Unit::findOrFail($id);
        return view('admin.units.form', compact('unit', 'title'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:255',
            'base_unit' => 'nullable|string|max:255',
            'conversion_factor' => 'required|numeric|min:0',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->name = $request->name;
        $unit->base_unit = $request->base_unit;
        $unit->conversion_factor = $request->conversion_factor;
        $unit->save();

        return redirect()->route('units.list')->with('success', 'Unit updated successfully.');
    }

    public function destroy($id){
        Unit::findOrFail($id)->delete();
        return redirect()->route('units.list')->with('success', 'Unit deleted successfully.');
    }
}
