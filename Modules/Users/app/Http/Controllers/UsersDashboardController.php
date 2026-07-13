<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Validator;
use Modules\Users\Models\UsersModel;
use Modules\Privilege\Models\PrivilegeModel;
use Modules\Branch\Models\BranchModel;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Core;

use Auth;
use DB;
use Image;
use Str;
use Carbon\Carbon;

class UsersDashboardController extends Controller
{
	public function index()
	{
		$privilege = Auth::user()->previlage;
		$main_id = 3;
       	$sub_id  = 67;
     
      	$option = DB::table('tbl_menu_set_options')
                        ->select('opset_options')
                        ->where('opset_privilege',$privilege)
                        ->where('opset_main_id',$main_id)
                        ->where('opset_sub_id',$sub_id)
                        ->first();
		
		 $permission = DB::table('privilege')
                        ->select('alloted_mainmenus','alloted_submenus')
                        ->where('status',0)
                        ->where('id',$privilege)
                        ->get();
                     
		$users = DB::table('users')
		          ->select('id','username','user_email','mobile','user_img','user_branch')
				  ->where('status',0)
			      ->orderby('id','desc')
				  ->get();	
		
		/**	if(Auth::user()->previlage != 2)
			{ **/
				$users = $users->where('user_branch',session('application_branch'));
		/**	} **/
		
		if(in_array($main_id,json_decode($permission[0]->alloted_mainmenus)))
    	{                    
   			return view ('users::user_dashboard')->with(['privilege'=>$privilege,'option'=>$option,'users'=>$users]);
    	}
   		else
    	{
        	return view('dashboard');
    	}

	}
  
}
