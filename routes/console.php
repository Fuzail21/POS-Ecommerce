<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use App\Models\Product;
use App\Models\User;
use App\Mail\LowStockAlert;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Daily low-stock email alert.
 * Finds all products below their threshold and emails all Admin users.
 */
Schedule::call(function () {
    $lowStockProducts = Product::with('inventoryStock')
        ->whereNull('deleted_at')
        ->get()
        ->filter(fn($product) => $product->is_low_stock);

    if ($lowStockProducts->isEmpty()) {
        return;
    }

    $adminEmails = User::whereHas('role', fn($q) => $q->where('name', 'Admin'))
        ->pluck('email');

    foreach ($adminEmails as $email) {
        Mail::to($email)->send(new LowStockAlert($lowStockProducts));
    }
})->dailyAt('08:00')->name('low-stock-alert')->withoutOverlapping();
