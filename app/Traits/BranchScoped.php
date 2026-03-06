<?php

namespace App\Traits;

trait BranchScoped
{
    /**
     * Returns the branch_id to filter by, or null if the user can see all branches.
     * Admin and Manager roles see all branches; other roles see only their own branch.
     */
    protected function getAuthBranchId(): ?int
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        $roleName = $user->role?->name;

        if (in_array($roleName, ['Admin', 'Manager'])) {
            return null; // no restriction
        }

        return $user->branch_id;
    }

    /**
     * Applies a branch_id WHERE clause to the given query if the current user
     * is not an Admin or Manager.
     */
    protected function applyBranchScope($query, string $column = 'branch_id')
    {
        $branchId = $this->getAuthBranchId();

        if ($branchId !== null) {
            $query->where($column, $branchId);
        }

        return $query;
    }
}
