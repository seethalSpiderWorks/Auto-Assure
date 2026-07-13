<?php

namespace Modules\Rules\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Rules\Database\Factories\RulesModelFactory;

class RulesModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): RulesModelFactory
    {
        //return RulesModelFactory::new();
    }
	
	
	
	protected $table = 'tbl_menu_main';
    protected $guarded = [];
  
    
     public function getRuleIdAttribute($mainId) {
        if (!empty($mainId)) {
            return Hashids::encode($mainId);
        }
    }
    
    public function BranchesData()
    {
        return $this->hasMany('Modules\Branches\Models\BranchesModel', 'sub_main_id');
    }
    
    public function SubMenus()
    {
        $results = $this->hasMany('Modules\Rules\Models\RulesubModel', 'sub_main_id');
        return $results;
    }
	
	
	
}
