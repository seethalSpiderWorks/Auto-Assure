<?php

namespace Modules\Privilege\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Privilege\Database\Factories\CompanyModelFactory;

class MenuPrivilege extends Model
{
    //
    protected $table = 'menu_privilege';
    protected $guarded = [];
}
