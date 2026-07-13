<?php

namespace Modules\Leads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Leads\Database\Factories\RegistrationModelFactory;

class RegistrationModel extends Model
{
    use HasFactory;
	protected $table = 'tbl_basic_registration';
    protected $guarded = [];
}
