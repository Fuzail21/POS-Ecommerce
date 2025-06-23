<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CashRegister;

class CheckOpenRegister
{
    public function handle(Request $request, Closure $next): mixed
    {
        $userId = Auth::id();

        $hasOpenRegister = CashRegister::where('user_id', $userId)
                                       ->whereNull('closed_at')
                                       ->exists();

        if (!$hasOpenRegister) {
            return redirect()->route('dashboard')->with('error', 'You must open a register before accessing POS.');
        }

        return $next($request);
    }
}
