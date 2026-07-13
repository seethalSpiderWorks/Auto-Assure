<?php

namespace Modules\Security\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Modules\Security\Models\SecurityModel;
use App\User ;
use App\Http\Controllers\Core;
use DataTables,Auth,DB;


class SecurityController extends Controller
{
    public function indexAction()
	{
		return view ('security::iplist');
    }
 
	public function getAllBlockedipsAction(Request $request)
	{
    	$limit = ($request->length != '') ? $request->length : 10;
        $offset = ($request->start != '') ? $request->start : 0;
    
        $search = $request->search['value'];
        $order = $request->order;
        $columns = $request->columns;
        $colName = 'tbl_user_login_ip.id';
        $sort = '';
        $ipAddress = $request->ip_address;
        //dd($ipAddress);
        if (isset($order[0]['column']) && isset($order[0]['dir'])) 
		{
            $colNo = $order[0]['column'];
            $sort = $order[0]['dir'];
            if (isset($columns[$colNo]['name'])) 
			{
                $colName = $columns[$colNo]['name'];
            }
        }
		
        $ip = SecurityModel::select('tbl_user_login_ip.id','tbl_user_login_ip.id as ip_id','tbl_user_login_ip.user_login_on','tbl_user_login_ip.user_login_time','tbl_user_login_ip.user_login_ip','tbl_user_login_ip.user_login_status')
                ->where('tbl_user_login_ip.user_login_status','0')
                ->Where(function($query) use ($search,$ipAddress) 
						{
                    		$query->where('tbl_user_login_ip.user_login_ip', 'like', $search . '%');
							///$query->orwhere('tbl_user_login_ip.user_login_on', 'like', $search . '%');
							$query->where('tbl_user_login_ip.user_login_ip', 'like', $ipAddress . '%');
                        });

//  $ip = SecurityModel::select('tbl_user_login_ip.id','tbl_user_login_ip.id as ip_id','tbl_user_login_ip.user_login_on','tbl_user_login_ip.user_login_time','tbl_user_login_ip.user_login_ip','tbl_user_login_ip.user_login_status')
//                 ->where('tbl_user_login_ip.user_login_status','0')
//                 ->where(function($query) use ($search) {
//                     $query->where('tbl_user_login_ip.user_login_ip', 'like', $search . '%');
//                       });
                      
        if ($colName != '' && $sort != '') 
		{
            $ip->orderBy($colName, $sort);
        } 
		else 
		{
            $ip->orderBy('tbl_user_login_ip.user_login_ip', 'asc');
        }
        $data = ["iTotalDisplayRecords" => $ip->count(), "iTotalRecords" =>  $ip->count(), "TotalDisplayRecords" => $limit];
        $data['data'] = $ip->skip($offset)->take($limit)->get()->toArray();
        
        return response()->json($data);
    }

	
    public function getAllBlockedipsAction11(Request $request)
	{
    	$limit = ($request->length != '') ? $request->length : 10;
        $offset = ($request->start != '') ? $request->start : 0;
        $search = $request->search['value'];
		//dd($search);
        $order = $request->order;
        $columns = $request->columns;
        $colName = 'tbl_user_login_ip.id';
        $sort = '';
        $ipAddress = $request->ip_address;
        
        if (isset($order[0]['column']) && isset($order[0]['dir']))
		{
            $colNo = $order[0]['column'];
            $sort = $order[0]['dir'];
            if (isset($columns[$colNo]['name'])) 
			{
                $colName = $columns[$colNo]['name'];
            }
        }
		
        $ip = SecurityModel::select('tbl_user_login_ip.id','tbl_user_login_ip.id as ip_id','tbl_user_login_ip.user_login_on','tbl_user_login_ip.user_login_time','tbl_user_login_ip.user_login_ip','tbl_user_login_ip.user_login_status')
                ->where('tbl_user_login_ip.user_login_status','0')
                ->Where(function($query) use ($ipAddress)
						{                                   //  'name', 'LIKE', "%" . $queryString . "%"
                   			 //$query->where('tbl_user_login_ip.user_login_ip','LIKE', "%" . $search . "%");
							 //$query->where('tbl_user_login_ip.user_login_on', 'LIKE', "%" . $search . "%");
							$query->where('tbl_user_login_ip.user_login_ip', 'like', $ipAddress . '%');
                        });

//  $ip = SecurityModel::select('tbl_user_login_ip.id','tbl_user_login_ip.id as ip_id','tbl_user_login_ip.user_login_on','tbl_user_login_ip.user_login_time','tbl_user_login_ip.user_login_ip','tbl_user_login_ip.user_login_status')
//                 ->where('tbl_user_login_ip.user_login_status','0')
//                 ->where(function($query) use ($search) {
//                     $query->where('tbl_user_login_ip.user_login_ip', 'like', $search . '%');
//                       });
                      
        if ($colName != '' && $sort != '') 
		{
            $ip->orderBy($colName, $sort);
        } 
		else 
		{
             $ip->orderBy('tbl_user_login_ip.user_login_ip', 'asc');
        }
		
	   $data = ["iTotalDisplayRecords" => $ip->count(), "iTotalRecords" => $ip->count(), "TotalDisplayRecords" => $limit];
       $dataMod = $ip->skip($offset)->take($limit)->get();
       $data['data'] = $dataMod->toArray();
		
        //$data = ["iTotalDisplayRecords" => $ip->count(), "iTotalRecords" =>  $ip->count(), "TotalDisplayRecords" => $limit];
        //$data['data'] = $ip->skip($offset)->take($limit)->get()->toArray();
        
        return response()->json($data);
    }

	public function unblockIpAction(Request $request)
	{
		//$ipId = Core::decodeId( $request->id);
		$ipId = $request->id;
		if($ipId)
		{
			SecurityModel::where(['id'=>$ipId])->update(['user_login_status'=>'1']);
				$ip_address = SecurityModel::select('user_login_ip')->where(['id'=>$ipId])->first();
		        $ip=$request->ip();
                $action = '';
                $user_name=Auth::user()->name;
                $userName = Auth::user()->username;
                $user_id = Auth::user()->id;
                $activity =  '#'.$user_name.'('.$userName.') Has Unblocked IP : '.$ip_address['user_login_ip'];
                $log_array = array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
                Core::userActivityAction($log_array);  
		
			return response()->json(['status' => 1, 'msg' => 'Updated successfully!', 'heading' => 'Success']);
		}
	}

	public function indexUserAction()
	{
		$fltrsts = DB::table('tbl_userblock_category')
					->select('cat_name','cat_id')
					->get();
		return view ('security::userlist',compact('fltrsts'));
	}

	public function getAllBlockedUsersAction(Request $request)
	{
        $limit = ($request->length != '') ? $request->length : 10;
        $offset = ($request->start != '') ? $request->start : 0;
        $search = $request->search['value'];  //dd($search);
        $order = $request->order;
        $columns = $request->columns;
        $colName = 'users.id';
        $sort = '';
		$privilege = Auth::user()->previlage;
		
		$userId = $request->user_id;
        $userName = $request->user_name;
        $status = $request->status;
		//dd($userName);
        if (isset($order[0]['column']) && isset($order[0]['dir'])) 
		{
            $colNo = $order[0]['column'];
            $sort = $order[0]['dir'];
            if (isset($columns[$colNo]['name'])) 
			{
                $colName = $columns[$colNo]['name'];
            }
        }
		
        $user = DB::table('users')
				->select('users.name','users.user_id','users.status','users.id','users.user_email','users.mobile','privilege.privilege_name')
				->join('privilege','privilege.id','=','users.previlage')
                ->where('users.status','!=','1')
				->where('user_branch',session('application_branch'))
                ->Where(function($query) use ($userId,$userName,$status) 
				   {  //dd($status);
                      /*	$query->where('users.name', 'like', $search . '%');
                    	$query->orWhere('users.user_id', 'like', $search . '%');
					    $query->orWhere('users.mobile', 'like', $search . '%');
					    $query->orWhere('users.user_email', 'like', $search . '%');
					    $query->orWhere('privilege.privilege_name', 'like', $search . '%'); */
					    $query->where('users.name', 'like', $userName . '%');
                    	$query->Where('users.user_id', 'like', $userId . '%');
					    if($status !='all')
						{
                       		$query->where('users.status', 'like', $status . '%');
                   		}
                   });

        if ($colName != '' && $sort != '') 
		{
            $user->orderBy($colName, $sort);
        }
		else 
		{
             $user->orderBy('users.id', 'desc');
        }
		
        $data = ["iTotalDisplayRecords" => $user->count(), "iTotalRecords" =>  $user->count(), "TotalDisplayRecords" => $limit];
        $data['data'] = $user->skip($offset)->take($limit)->get()->toArray();
       
        return response()->json($data);
	}

	public function statusChangeAction(Request $request)
	{
		$id = $request->id;
		$status = $request->status;

		$update = DB::table('users')->where(['id'=>$id])->update(['status'=>$status]) ;
		if($update)
		{
			$user =  DB::table('users')->select('name')->where(['id'=>$id])->first();
		        $ip=$request->ip();
                $action = '';
                $user_name=Auth::user()->name;
                $userName = Auth::user()->username;
                $user_id = Auth::user()->id;
               if($status == 0)
			   {
               			 $activity =  '#'.$user_name.'('.$userName.') Has Unblocked User : '.$user->name;
				         $msg = "User unblocked successfully";
			   }
			   else
			   {
						$activity =  '#'.$user_name.'('.$userName.') Has Blocked User : '.$user->name;
				        $msg = "User blocked successfully";
			   }      
			
			$log_array = array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
              Core::userActivityAction($log_array);
      return response()->json(['status' => 1, 'msg' => $msg, 'heading' => 'Success']);
	}
	else
	{
		return response()->json(['status' => 0, 'msg' => 'Something went wrong try again!', 'heading' => 'Success']);
	}

    }
}


