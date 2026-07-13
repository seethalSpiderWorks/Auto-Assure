<?php

namespace Modules\Rules\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SetoptionsModel extends Model
{
    protected $table = 'tbl_menu_set_options';
      protected $guarded = [];
    //
    
     public function getDivisionIdAttribute($id) {
        if (!empty($id)) {
            return Hashids::encode($id);
        }
    }
}
