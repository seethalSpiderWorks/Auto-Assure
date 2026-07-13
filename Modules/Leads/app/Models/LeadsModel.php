<?php

namespace Modules\Leads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Leads\Database\Factories\LeadsModelFactory;

class LeadsModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_lead';
    protected $guarded = [];
}
