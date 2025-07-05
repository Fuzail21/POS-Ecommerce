<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $title = 'Categories List';
        $categories = Category::with('parent')->latest()->paginate(20);
        return view('admin.category.list', compact('categories', 'title'));
    }

    public function create(){
        $title = 'Add Category';
        $parents = Category::whereNull('parent_id')->get();
        return view('admin.category.form', compact('parents', 'title'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create($request->all());
        return redirect()->route('categories.list')->with('success', 'Category added.');
    }

    public function edit($id){
        $title = 'Edit Category';
        $category = Category::findOrFail($id);
        $parents = Category::whereNull('parent_id')->where('id', '!=', $id)->get();
        return view('admin.category.form', compact('category', 'parents', 'title'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
    
        $data = $request->all();
    
        // Convert empty parent_id to null
        if (empty($data['parent_id'])) {
            $data['parent_id'] = null;
        }
    
        // Find the category by ID
        $category = Category::findOrFail($id);
    
        // Update the category
        $category->update($data);
    
        return redirect()->route('categories.list')->with('success', 'Category updated.');
    }

    public function destroy($id){
        $category = Category::findOrFail($id);

        // Check if this category has child categories
        $hasChildren = Category::where('parent_id', $id)->exists();

        if ($hasChildren) {
            return redirect()->route('categories.list')->with('error', 'Cannot delete category with child categories.');
        }

        $category->delete();

        return redirect()->route('categories.list')->with('success', 'Category deleted.');
    }

}

