<?php

namespace Modules\Security\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Security\Database\Factories\UserLogModelFactory;

class UserLogModel extends Model
{
    use HasFactory;

    protected $table = 'user_activity_logs';
    protected $guarded = [];
}
