<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::paginate(20);
        $title = 'Roles List';
        return view('admin.roles.list', compact('roles', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Add New Role';
        return view('admin.roles.form', compact('title'));
    }

   // Store the role
   public function store(Request $request)
   {
       $request->validate([
           'name' => 'required|string|max:255|unique:roles,name',
           'description' => 'nullable|string|max:1000',
       ]);

       try {
           $role = new Role();
           $role->name = $request->name;
           $role->description = $request->description;
           $role->save();

           return redirect()->route('role.list')->with('success', 'Role added successfully!');
       } catch (\Exception $e) {
           return redirect()->route('role.list')->with('error', 'Something went wrong! Please try again.');
       }
   }

   // Show the edit form
   public function edit($id)
   {
       $role = Role::findOrFail($id);
       return view('admin.roles.form', compact('role'));
   }

   // Update the role
   public function update(Request $request, $id)
   {
       $request->validate([
           'name' => 'required|string|max:255|unique:roles,name,' . $id,
           'description' => 'nullable|string|max:1000',
       ]);

       try {
           $role = Role::findOrFail($id);
           $role->name = $request->name;
           $role->description = $request->description;
           $role->save();

           return redirect()->route('role.list')->with('success', 'Role updated successfully!');
       } catch (\Exception $e) {
           return redirect()->route('role.list')->with('error', 'Something went wrong! Please try again.');
       }
   }


     // Delete the role
     public function destroy($id)
     {
         try {
             $role = Role::findOrFail($id);
             $role->delete();
 
             return redirect()->route('role.list')->with('success', 'Role deleted successfully!');
         } catch (\Exception $e) {
             return redirect()->route('role.list')->with('error', 'Unable to delete role. Please try again.');
         }
     }
}
