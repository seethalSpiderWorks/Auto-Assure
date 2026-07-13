<?php

namespace Modules\Branch\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Branch\Database\Factories\BranchesModelFactory;

class BranchesModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
	protected $table = 'tbl_branch';
    protected $guarded = [];
    protected $fillable = [];

    protected static function newFactory(): BranchesModelFactory
    {
        //return BranchesModelFactory::new();
    }
}
