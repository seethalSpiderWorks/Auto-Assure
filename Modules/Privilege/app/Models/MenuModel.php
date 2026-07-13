<?php

namespace Modules\Privilege\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Privilege\Database\Factories\CompanyModelFactory;

class MenuModel extends Model
{
    //
    protected $table = 'tbl_menus';
    protected $guarded = [];

     public function child_permissions() {
        return $this->hasMany('Modules\Privilege\Models\MenuModel','parent_id', 'main_id');
    }
}
