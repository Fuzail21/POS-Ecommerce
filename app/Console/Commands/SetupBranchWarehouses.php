<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupBranchWarehouses extends Command
{
    protected $signature   = 'branches:setup-warehouses';
    protected $description = 'Create a dedicated store-warehouse for each branch that shares a central warehouse, then re-link the branch to its own warehouse.';

    public function handle(): int
    {
        $branches = Branch::with('warehouse')->get();

        $warehouseUseCounts = $branches->groupBy('warehouse_id')->map->count();

        $this->table(
            ['Branch', 'Current Warehouse', 'Shared?'],
            $branches->map(fn($b) => [
                $b->name,
                $b->warehouse->name ?? '—',
                ($warehouseUseCounts[$b->warehouse_id] ?? 1) > 1 ? 'YES (shared)' : 'no',
            ])
        );

        if (! $this->confirm('Create a dedicated store-warehouse for EACH branch and re-link it?', true)) {
            $this->info('Cancelled.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($branches) {
            foreach ($branches as $branch) {
                // Skip if this branch is already the sole user of its warehouse
                $isShared = Branch::where('warehouse_id', $branch->warehouse_id)
                                  ->where('id', '!=', $branch->id)
                                  ->exists();

                if (! $isShared) {
                    $this->line("  ✓ {$branch->name} — already has its own warehouse, skipped.");
                    continue;
                }

                // Create a new store-level warehouse for this branch
                $store = Warehouse::create([
                    'name'          => $branch->name . ' اسٹور',
                    'location'      => $branch->location,
                    'capacity'      => 5000,
                    'capacity_unit' => 'units',
                    'used_capacity' => 0,
                ]);

                $branch->update(['warehouse_id' => $store->id]);

                $this->line("  ✓ {$branch->name} → new warehouse: {$store->name} (id {$store->id})");
            }
        });

        $this->newLine();
        $this->info('Done! All branches now have their own dedicated store warehouses.');
        $this->warn('Use Stock Transfer to move products from central warehouses to each branch store before selling.');

        return self::SUCCESS;
    }
}
