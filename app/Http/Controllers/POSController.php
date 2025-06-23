<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegister;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\Expense;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Auth;


class POSController extends Controller
{
    // Method 1: Open Register
    public function openRegister(Request $request){
        $request->validate(['opening_cash' => 'required|numeric|min:0']);

        // Prevent multiple open registers
        $existingRegister = CashRegister::where('user_id', auth()->id())
                                        ->whereNull('closed_at')
                                        ->first();

        if ($existingRegister) {
            return response()->json([
                'success' => false,
                'message' => 'A register is already open.'
            ], 409);
        }

        $register = CashRegister::create([
            'user_id' => auth()->id(),
            'opening_cash' => $request->opening_cash,
            'opened_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('pos.index')
        ]);
    }

    // Method 2: Close Register
    public function closeRegister() {
        $userId = auth()->id();
    
        $register = CashRegister::where('user_id', $userId)
                ->whereNull('closed_at')
                ->latest()
                ->first();
    
        if (!$register) {
            return response()->json([
                'success' => false,
                'message' => 'Register is already closed.'
            ], 400);
        }
    
        $start = $register->opened_at;
        $end = now();
    
        $salesTotal = Sale::where('created_by', $userId)
                          ->whereBetween('created_at', [$start, $end])
                          ->sum('final_amount');
    
        $expenseTotal = Expense::where('created_by', $userId)
                               ->whereBetween('created_at', [$start, $end])
                               ->sum('amount');
    
        $closingCash = $register->opening_cash + $salesTotal - $expenseTotal;
    
        $register->update([
            'total_sales'     => $salesTotal,
            'total_expense'   => $expenseTotal,
            'closing_cash'    => $closingCash,
            'cash_difference' => $closingCash - $register->opening_cash,
            'closed_at'       => $end,
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Register closed successfully.',
            'redirect' => route('dashboard'),
        ]);
    }

    // Method 3: Check if Register is Open
    public function checkRegister(){
        $isOpen = CashRegister::where('user_id', auth()->id())
                              ->whereNull('closed_at')
                              ->exists();

        return response()->json(['open' => $isOpen]);
    }

    // Method 4: Get Register Details
    public function getRegisterDetails(Request $request){
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized: Please log in to view register details.',
                'details' => null
            ], 401);
        }

        $userId = Auth::id();

        $register = CashRegister::where('user_id', $userId)
                                ->whereNull('closed_at')
                                ->first();

        if (!$register) {
            $register = CashRegister::where('user_id', $userId)
                                    ->whereNotNull('closed_at')
                                    ->orderBy('closed_at', 'desc')
                                    ->first();
        }

        if (!$register) {
            return response()->json([
                'message' => 'No register found.',
                'details' => [
                    'payment_type' => [
                        'Cash In Hand'   => '$ 0.00',
                        'Cash'           => '$ 0.00',
                        'Card'           => '$ 0.00',
                        'Bank'           => '$ 0.00',
                    ],
                    'total_sales'   => '$ 0.00',
                    'total_refund'  => '$ 0.00',
                    'total_payment' => '$ 0.00',
                    'date' => now()->format('F jS Y'),
                ]
            ], 404);
        }

        $registerStatus = $register->closed_at ? 'closed' : 'open';
        $start = Carbon::parse($register->opened_at);
        $end = $register->closed_at ? Carbon::parse($register->closed_at) : now();

        $paymentTypeTotals = [
            'cash' => 0,
            'card' => 0,
            'bank' => 0,
        ];
        $totalSales = 0;
        $totalRefund = 0;

        $payments = Payment::where('created_by', $userId)
                            ->whereBetween('created_at', [$start, $end])
                            ->get();

        foreach ($payments as $payment) {
            $amount = (float) $payment->amount;
            $method = strtolower($payment->payment_method);

            switch ($payment->ref_type) {
                case 'sale':
                    $totalSales += $amount;
                    if (isset($paymentTypeTotals[$method])) {
                        $paymentTypeTotals[$method] += $amount;
                    } else {
                        $paymentTypeTotals['other'] = ($paymentTypeTotals['other'] ?? 0) + $amount;
                    }
                    break;

                case 'sales_return':
                    $totalRefund += $amount;
                    break;

                case 'payment':
                    if (isset($paymentTypeTotals[$method])) {
                        $paymentTypeTotals[$method] += $amount;
                    } else {
                        $paymentTypeTotals['other'] = ($paymentTypeTotals['other'] ?? 0) + $amount;
                    }
                    break;
            }
        }

        $totalPayment = $register->opening_cash + $totalSales - $totalRefund;
        $format = fn($amt) => '$ ' . number_format($amt, 2);

        return response()->json([
            'message' => 'Register details fetched successfully.',
            'details' => [
                'payment_type' => [
                    'Cash In Hand' => $format($register->opening_cash),
                    'Cash'         => $format($paymentTypeTotals['cash']),
                    'Card'         => $format($paymentTypeTotals['card']),
                    'Bank'         => $format($paymentTypeTotals['bank']),
                ],
                'total_sales'   => $format($totalSales),
                'total_refund'  => $format($totalRefund),
                'total_payment' => $format($totalPayment),
                'date' => $registerStatus === 'open'
                            ? now()->format('F jS Y')
                            : Carbon::parse($register->closed_at)->format('F jS Y'),
            ]
        ]);
    }
}
