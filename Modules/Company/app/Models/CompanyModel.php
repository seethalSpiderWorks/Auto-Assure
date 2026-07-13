<?php

namespace Modules\Company\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Company\Database\Factories\CompanyModelFactory;

class CompanyModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): CompanyModelFactory
    {
        //return CompanyModelFactory::new();
    }
	
	protected $table = 'tbl_company';
    protected $primaryKey = 'company_id';
      
    public function BranchesData()
    {
        return $this->hasMany('Modules\Branch\Models\BranchModel', 'company_id','company_id');
    }
	
}
