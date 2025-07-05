<?php

namespace App\Http\Controllers;
use App\Models\Company;
use App\Models\User;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $companies = Company::paginate(20);
        $title = 'Company List';
        return view('admin.company.list', compact('companies', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $title = 'Add New Company';
        return view('admin.company.add', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        try {
            $company = new Company();
            $company->name = $request->name;
            $company->save();
    
            return redirect()->route('company.list')->with('success', 'Company added successfully!');
        } catch (\Exception $e) {
            return redirect()->route('company.list')->with('error', 'Something went wrong! Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id){
        $title = 'Edit Company';
        $company = Company::find($id);
        return view('admin.company.edit', compact('company', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id){
        try {
            $company = Company::find($id);
            $company->name = $request->name;
            $company->save();
    
            return redirect()->route('company.list')->with('success', 'Company updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('company.list')->with('error', 'Something went wrong! Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id){
        $company = Company::find($id);
        if ($company) {
            $company->delete();
            return redirect()->route('company.list')->with('success', 'Company deleted successfully!');
        } else {
            return redirect()->route('company.list')->with('error', 'Company not found!');
        }
    }
}
