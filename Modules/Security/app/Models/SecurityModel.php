<?php

namespace Modules\Security\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Security\Database\Factories\SecurityModelFactory;

class SecurityModel extends Model
{
    use HasFactory;

    protected $table      = 'tbl_user_login_ip';
    protected $primaryKey = 'id';
}
