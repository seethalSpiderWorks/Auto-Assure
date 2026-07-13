<?php

namespace Modules\Rules\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Rules\Database\Factories\RulesubModelFactory;

class RulesubModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): RulesubModelFactory
    {
        //return RulesubModelFactory::new();
    }
	
	protected $table = 'tbl_menu_sub';
    protected $guarded = [];
      
       public function getRulesubIdAttribute($sub_id) {
        if (!empty($sub_id)) {
            return Hashids::encode($sub_id);
        }
    }
	
	
}
