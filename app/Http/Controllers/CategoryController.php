<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::paginate(20);
        $title = 'Categories List';
        return view('admin.category.list', compact('categories', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Add New Category';
        return view('admin.category.add', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        try {
            $category = new Category();
            $category->name = $request->name;
            $category->save();
    
            return redirect()->route('category.list')->with('success', 'Category added successfully!');
        } catch (\Exception $e) {
            return redirect()->route('category.list')->with('error', 'Something went wrong! Please try again.');
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
        $title = 'Edit Category';
        $category = Category::find($id);
        return view('admin.category.edit', compact('category', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $category = Category::find($id);
            $category->name = $request->name;
            $category->save();
    
            return redirect()->route('category.list')->with('success', 'Category updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('category.list')->with('error', 'Something went wrong! Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return redirect()->route('category.list')->with('success', 'Category deleted successfully!');
        } else {
            return redirect()->route('category.list')->with('error', 'Category not found!');
        }
    }
}
