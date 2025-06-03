<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(){
        $users = User::with('role')->paginate(20);
        $title = 'Users List';
        return view('admin.users.list', compact('users', 'title'));
    }

    public function create(){
        $branches = Branch::all();
        $roles = Role::all();
        $title = 'Add New User';
        return view('admin.users.form', compact('title', 'roles', 'branches'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'status' => 'required|in:Active,Inactive',
            'password' => 'nullable|string|min:6', // optional, min 6 characters
            'branch_id' => 'nullable|exists:branches,id',
        ]);
    
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->branch_id = $request->branch_id;
        $user->status = $request->status;
        $user->password = Hash::make($request->password);
        $user->save();
    
        return redirect()->route('user.list')->with('success', 'User added successfully.');
    }

    public function edit(string $id){
        $branches = Branch::all();
        $roles = Role::all();
        $title = 'Edit User';
        $user = User::find($id);
        return view('admin.users.form', compact('user', 'title', 'roles', 'branches'));
    }

    public function update(Request $request, string $id){
        $user = User::find($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'status' => 'required|in:Active,Inactive',
            'password' => 'nullable|string|min:6', // optional, min 6 characters
            'branch_id' => 'nullable|exists:branches,id',
        ]);
    
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->branch_id = $request->branch_id;
        $user->status = $request->status;

        // Only update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
    
        return redirect()->route('user.list')->with('success', 'User updated successfully.');
    }

    public function destroy(string $id){
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return redirect()->route('user.list')->with('success', 'User deleted successfully!');
        } else {
            return redirect()->route('user.list')->with('error', 'User not found!');
        }
    }
}
