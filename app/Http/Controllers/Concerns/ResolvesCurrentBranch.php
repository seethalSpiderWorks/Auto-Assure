<?php

namespace App\Http\Controllers\Concerns;

use Modules\Branch\Models\BranchModel;

/**
 * Resolves the CRM user's current branch (from the session/branch the CRM
 * already tracks) for page context. Uses the existing Branch module model
 * (`tbl_branch`). Kept lenient so the inspection screens are never blocked.
 */
trait ResolvesCurrentBranch
{
    protected function requireBranch(): ?BranchModel
    {
        $branchId = session('branch') ?: optional(request()->user())->user_branch;

        return ($branchId ? BranchModel::find($branchId) : null)
            ?? BranchModel::query()->first();
    }
}
