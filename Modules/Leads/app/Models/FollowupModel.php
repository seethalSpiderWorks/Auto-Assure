<?php

namespace Modules\Leads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Leads\Database\Factories\FollowupModelFactory;

class FollowupModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_lead_followup';
    protected $guarded = [];
}
