<?php

namespace Modules\Branch\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Branch\Database\Factories\BranchModelFactory;

class BranchModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
	protected $table = 'tbl_branch';
    protected $primaryKey = 'branch_id';
    protected $fillable = [];

    protected static function newFactory(): BranchModelFactory
    {
        //return BranchModelFactory::new();
    }
}
