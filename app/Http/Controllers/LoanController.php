<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\User;
use App\Models\Customer;
use App\Models\Vendor;



class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::paginate(20);

        $loans->getCollection()->transform(function ($loan) {
            if ($loan->user_type === 'customer') {
                $user = Customer::find($loan->user_id);
            } elseif ($loan->user_type === 'vendor') {
                $user = Vendor::find($loan->user_id);
            } else {
                $user = null;
            }

            $loan->user_name = $user ? $user->name : 'Unknown';
            return $loan;
        });

        $title = 'Loans List';
        return view('admin.loan.list', compact('loans', 'title'));
    }


    public function create()
    {
        $title = 'Add New Loan';
        return view('admin.loan.add', compact('title'));
    }

    public function store(Request $request)
    {
        try 
        {
            $request->validate([
                'user_type' => 'required|in:vendor,customer',
                'user_id' => 'required|integer',
                'amount' => 'required|numeric',
                // 'status' => 'required|in:pending,paid',
            ]);

            $loan = new Loan();
            $loan->user_type = $request->user_type;
            $loan->user_id = $request->user_id;
            $loan->amount = $request->amount;
            $loan->status = "pending";
            // $loan->status = $request->status;
            $loan->save();

            return redirect()->route('loan.list')->with('success', 'Loan created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('loan.list')->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function edit($id)
    {
        $loan = Loan::findOrFail($id);
        $title = 'Edit Loan';
    
        // Load users based on loan user_type
        if ($loan->user_type === 'customer') {
            $users = \App\Models\Customer::all(['id', 'name']);
        } elseif ($loan->user_type === 'vendor') {
            $users = \App\Models\Vendor::all(['id', 'name']);
        } else {
            $users = [];
        }
    
        return view('admin.loan.edit', compact('loan', 'users', 'title'));
    }


    public function update(Request $request, $id)
    {
        try 
        {
            $request->validate([
                'user_type' => 'required|in:vendor,customer',
                'user_id' => 'required|integer',
                'amount' => 'required|numeric',
                // 'status' => 'required|in:pending,paid',
            ]);

            $loan = Loan::findOrFail($id);
            $loan->user_type = $request->user_type;
            $loan->user_id = $request->user_id;
            $loan->amount = $request->amount;
            $loan->status = "pending";
            // $loan->status = $request->status;
            $loan->save();

            return redirect()->route('loan.list')->with('success', 'Loan updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('loan.list')->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function destroy($id)
    {
        $loan = Loan::find($id);
        if ($loan) {
            // Delete the store
            $loan->delete();
    
            // Redirect back with a success message
            return redirect()->route('loan.list')->with('success', 'Loan deleted successfully!');
        } else {
            // Redirect back with an error message if store not found
            return redirect()->route('loan.list')->with('error', 'Loan not found!');
        }
    }


    public function getUsersByType(Request $request)
    {
        $userType = $request->user_type;

        if ($userType === 'customer') {
            $users = Customer::all(['id', 'name']);
        } elseif ($userType === 'vendor') {
            $users = Vendor::all(['id', 'name']);
        } else {
            return response()->json([], 400); // Bad request
        }
        return response()->json($users);
    }
}
