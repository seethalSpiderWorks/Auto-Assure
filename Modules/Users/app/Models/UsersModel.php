<?php

namespace Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Users\Database\Factories\UsersModelFactory;

class UsersModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    protected static function newFactory(): UsersModelFactory
    {
        //return UsersModelFactory::new();
    }
	
	protected $table='users';
	protected $primaryKey='id';
}
