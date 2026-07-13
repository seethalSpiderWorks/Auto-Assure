<?php

namespace Modules\Registration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Core;
use DataTables,Auth,DB;

class RegistrationController extends Controller
{
    public function viewIndex()
    {
        return view('registration::view_index');
    }
	
	public function datatable(Request $request)
    {
		$current_route = \Route::current()->uri();
		$privilege = Auth::user()->previlage;		
        $foll_id   = $request->id;    

		$main_id = 46;
		//$sub_id  = 52;
     
		$option = DB::table('tbl_menu_set_options')
				->select('opset_options')
				->where('opset_privilege',$privilege)
				->where('opset_main_id',$main_id)
				//->where('opset_sub_id',$sub_id)
				->first();		

		$limit  = ($request->length != '') ? $request->length : 10;
		$offset = ($request->start != '') ? $request->start : 0;
		$search = $request->search['value'];
		
        $area  = DB::table('tbl_registration')
				->select('reg_id', 'reg_date', 'reg_fname', 'reg_mob', 'reg_email')
                ->orderBy('reg_id','desc')
                ->where('reg_status',0)
                ->Where(function($query) use ($search) 
                	{
						$query->where('reg_fname', 'like', $search . '%');
						$query->orwhere('reg_mob', 'like', $search . '%');
						$query->orwhere('reg_email', 'like', $search . '%');
                    });
		 	
		$data = ["iTotalDisplayRecords"=>$area->count(), "iTotalRecords"=>$area->count(), "TotalDisplayRecords"=>$limit,"option"=>$option];
		$dataMod = $area->skip($offset)->take($limit)->get();
		$data['data'] = $dataMod->toArray();
	
		return response()->json($data);
    }
	
	public function deleteRegistration(request $request)
    { 
		$delete = DB::table('tbl_registration')
			->where('reg_id',$request->id)
			->update(['reg_status' => 1]);
		
		$ip = $request->ip();
		$action = '';
		$user_name = Auth::user()->name;
		$user_id = Auth::user()->id;
		$activity = 'Registration has been deleted by '. $user_name.'';
		$log_array = array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id, 'activity_desc'=>$activity);  
		Core::userActivityAction($log_array);
		
		return response()->json(['status' => 1, 'msg' => 'Registration Deleted Successfully', 'heading' => 'Success']);
    }

     
}
