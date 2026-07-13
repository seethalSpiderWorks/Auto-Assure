<?php

namespace Modules\Security\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Core;
use Modules\Leads\Models\RegistrationModel;
use Modules\Leads\Models\LeadsModel;
use Modules\Leads\Models\FollowupModel;
use Modules\Leads\Models\SubModel;
use Modules\Security\Models\UserLogModel;
use DataTables,Auth,DB;

class UserLogController extends Controller
{
	public function indexAction()
    {
		session()->forget('from');
		session()->forget('to');
		
		return view ('security::user_log');
    }
  
 	public function getAllBranchAction(Request $request) 
 	{
        $limit = ($request->length != '') ? $request->length : 10;
        $offset = ($request->start != '') ? $request->start : 0;
        $search = $request->search['value'];
        $order = $request->order;
        $columns = $request->columns;
        $colName = 'user_activity_logs.id';
        $sort = '';
        
        if (isset($order[0]['column']) && isset($order[0]['dir']))
		{
            $colNo = $order[0]['column'];
            $sort = $order[0]['dir'];
            if (isset($columns[$colNo]['name'])) 
			{
                $colName = $columns[$colNo]['name'];
            }
        }

        $branch = UserLogModel::select('user_activity_logs.id','user_activity_logs.id as id','user_activity_logs.activity_ip','user_activity_logs.activity_user','user_activity_logs.activity_user_id','user_activity_logs.activity_desc','user_activity_logs.created_at','users.name')
               ->join('users', 'users.id','=','user_activity_logs.activity_user_id')
			   ->where('user_branch',session('application_branch'))
               ->Where(function($query) use ($search) 
					   {
                    		$query->where('users.name', 'like', $search . '%');
               		   });

		 //dd(session('from'));
			if(session('from') && session('to'))
            {
              	$branch->whereBetween('user_activity_logs.created_at',[session('from'),session('to')]);
            }
          
            else if(session('from'))
            {
                $branch->where('user_activity_logs.created_at','=',session('from'));
            } 
			else if(session('to'))
            {
                $branch->where('user_activity_logs.created_at','<=',session('to'));
            } 
		
			if ($colName != '' && $sort != '') 
			{
            	$branch->orderBy($colName, $sort);
        	} 
			else 
			{
				$branch->orderBy('user_activity_logs.created_at', 'ASC');
			}
		
		
			$datas = ["iTotalDisplayRecords" => $branch->count(), "iTotalRecords" => $branch->count(), "TotalDisplayRecords" => $limit];
			$dataMod = $branch->skip($offset)->take($limit)->get();
			$datas['data'] = $dataMod->toArray();
		
        	//$data = ["iTotalDisplayRecords" => $branch->count(), "iTotalRecords" => $branch->count(), "TotalDisplayRecords" => $limit];
        	//$data['data'] = $branch->skip($offset)->take($limit)->get()->toArray();
        
        	return response()->json($datas);
	 }
	
	public function filterAction(Request $request)
    {
		//dd($request->from_date);
		
        if($request->from_date)
        {
            session(['from'=>$request->from_date]);
        }
        else
        {
            session()->forget('from');
        }
        if($request->to_date)
        {
            session(['to'=>$request->to_date]);
        }
        else
        {
            session()->forget('to');
		}
       
		//session()->forget('filter_campaign');
    }
}
