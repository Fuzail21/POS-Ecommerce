<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // ── SALE PAYMENTS (money received from customers) ──────────────────────
        // Sale 1 — Walk-in, cash, fully paid
        DB::table('payments')->insert([
            'entity_type'      => 'customer',
            'entity_id'        => 1,   // Walk-in Customer
            'transaction_type' => 'in',
            'amount'           => 3730.00,
            'payment_method'   => 'cash',
            'ref_type'         => 'sale',
            'ref_id'           => 1,
            'note'             => 'Cash payment for INV-2026-0001',
            'created_by'       => 3,   // Cashier Bilal
            'created_at'       => '2026-02-10 10:30:00',
            'updated_at'       => '2026-02-10 10:30:00',
        ]);

        // Sale 2 — Ali Ahmad, card, fully paid
        DB::table('payments')->insert([
            'entity_type'      => 'customer',
            'entity_id'        => 2,   // Ali Ahmad Khan
            'transaction_type' => 'in',
            'amount'           => 86680.00,
            'payment_method'   => 'card',
            'ref_type'         => 'sale',
            'ref_id'           => 2,
            'note'             => 'Card payment for INV-2026-0002 (Samsung A54)',
            'created_by'       => 3,
            'created_at'       => '2026-02-11 14:00:00',
            'updated_at'       => '2026-02-11 14:00:00',
        ]);

        // Sale 3 — Ayesha, online/bank transfer, fully paid
        DB::table('payments')->insert([
            'entity_type'      => 'customer',
            'entity_id'        => 3,   // Ayesha Siddiqui
            'transaction_type' => 'in',
            'amount'           => 9490.00,
            'payment_method'   => 'bank',
            'ref_type'         => 'sale',
            'ref_id'           => 3,
            'note'             => 'Online payment for INV-2026-0003 (e-commerce order)',
            'created_by'       => 6,   // E-commerce manager
            'created_at'       => '2026-02-12 09:15:00',
            'updated_at'       => '2026-02-12 09:15:00',
        ]);

        // Sale 4 — Hassan Malik, cash, fully paid
        DB::table('payments')->insert([
            'entity_type'      => 'customer',
            'entity_id'        => 6,   // Hassan Malik
            'transaction_type' => 'in',
            'amount'           => 10170.00,
            'payment_method'   => 'cash',
            'ref_type'         => 'sale',
            'ref_id'           => 4,
            'note'             => 'Cash payment for INV-2026-0004 (bulk grocery)',
            'created_by'       => 3,
            'created_at'       => '2026-02-14 11:45:00',
            'updated_at'       => '2026-02-14 11:45:00',
        ]);

        // Sale 5 — Muhammad Umar, pending e-commerce — NO payment yet (due_amount = 32810.00)

        // ── PURCHASE PAYMENTS (money paid to suppliers) ────────────────────────
        // Purchase 1 — Al-Harmain Traders, fully paid via bank
        DB::table('payments')->insert([
            'entity_type'      => 'supplier',
            'entity_id'        => 1,   // Al-Harmain Traders
            'transaction_type' => 'out',
            'amount'           => 295000.00,
            'payment_method'   => 'bank',
            'ref_type'         => 'purchase',
            'ref_id'           => 1,
            'note'             => 'Full payment for PUR-2026-0001 (electronics)',
            'created_by'       => 1,   // Admin
            'created_at'       => '2026-02-01 12:00:00',
            'updated_at'       => '2026-02-01 12:00:00',
        ]);

        // Purchase 2 — Punjab Foods & Grains, partial payment (120,000 still due)
        DB::table('payments')->insert([
            'entity_type'      => 'supplier',
            'entity_id'        => 3,   // Punjab Foods & Grains
            'transaction_type' => 'out',
            'amount'           => 200000.00,
            'payment_method'   => 'bank',
            'ref_type'         => 'purchase',
            'ref_id'           => 2,
            'note'             => 'Partial payment for PUR-2026-0002 (grocery) — PKR 120,000 remaining',
            'created_by'       => 1,
            'created_at'       => '2026-02-03 15:30:00',
            'updated_at'       => '2026-02-03 15:30:00',
        ]);

        // Purchase 3 — HiTech Mobile Distributors, fully paid via bank
        DB::table('payments')->insert([
            'entity_type'      => 'supplier',
            'entity_id'        => 5,   // HiTech Mobile Distributors
            'transaction_type' => 'out',
            'amount'           => 1670000.00,
            'payment_method'   => 'bank',
            'ref_type'         => 'purchase',
            'ref_id'           => 3,
            'note'             => 'Full payment for PUR-2026-0003 (Samsung & Tecno mobiles)',
            'created_by'       => 1,
            'created_at'       => '2026-02-07 10:00:00',
            'updated_at'       => '2026-02-07 10:00:00',
        ]);

        // ── MANUAL PAYMENTS ────────────────────────────────────────────────────
        // Advance payment to Karachi Electronics Co. (supplier 2)
        DB::table('payments')->insert([
            'entity_type'      => 'supplier',
            'entity_id'        => 2,   // Karachi Electronics Co.
            'transaction_type' => 'out',
            'amount'           => 50000.00,
            'payment_method'   => 'bank',
            'ref_type'         => 'manual',
            'ref_id'           => null,
            'note'             => 'Advance payment to Karachi Electronics Co. for upcoming order',
            'created_by'       => 1,
            'created_at'       => '2026-02-15 09:00:00',
            'updated_at'       => '2026-02-15 09:00:00',
        ]);

        // Manual customer credit note — Samina Begum returned defective item
        DB::table('payments')->insert([
            'entity_type'      => 'customer',
            'entity_id'        => 7,   // Samina Begum
            'transaction_type' => 'out',
            'amount'           => 380.00,
            'payment_method'   => 'cash',
            'ref_type'         => 'manual',
            'ref_id'           => null,
            'note'             => 'Refund to Samina Begum for defective Pond\'s Cream',
            'created_by'       => 3,
            'created_at'       => '2026-02-18 16:00:00',
            'updated_at'       => '2026-02-18 16:00:00',
        ]);
    }
}
