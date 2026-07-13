<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
//use Modules\Company\Models\CompanyModel;
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

class UsersController extends Controller
{
	public function profile()
	{
		return view ('users::profile');
	}
	public function profile1(Request $request)
	{
		$id = $request->id;
		
		$data = DB::table('users')
			   ->where('status',0)
			   ->where('id',$id)
			   ->first();

		if($data)
		{
			return view ('users::contacts-profile',compact('id','data'));
		}
		else
		{
			return redirect('/');
		}
	}
	
    public function index()
	{
		$privilege = Auth::user()->previlage;
		//$centre = Auth::user()->user_branch;
		$centre = session('application_branch');
		$main_id = 3;
       	$sub_id  = 33;
     
      	$option = DB::table('tbl_menu_set_options')
                        ->select('opset_options')
                        ->where('opset_privilege',$privilege)
                        ->where('opset_main_id',$main_id)
                        ->where('opset_sub_id',$sub_id)
                        ->first();                        
		$privilage = PrivilegeModel::select('id','privilege_name')
					->where('status',0);
					if($privilege != 2)
					{
						$privilage->where('id','!=',2);	
					}
		$privilage = $privilage->get();
		
		$company = DB::table('tbl_company')
                      ->select('company_id','company_name')
                      ->where('company_status',0)
                      ->get();
					  
		$branch = BranchModel::select('branch_id','branch_name')
				->where('branch_status',0);
				if($privilege != 2)
				{
					$branch->where('branch_id',$centre);
				}
			   $branch->orderBy('branch_name','ASC');
			   $branch= $branch->get();
  
		$permission = DB::table('privilege')
                        ->select('alloted_mainmenus','alloted_submenus')
                        ->where('status',0)
                        ->where('id',$privilege)
                        ->get();
				                       
			//return view ('users::user_index')->with(['privilage'=>$privilage,'branch'=>$branch,'company'=>$company]);
		if(in_array($main_id,json_decode($permission[0]->alloted_mainmenus)))
    	{                    
   			return view ('users::user_index')->with(['privilage'=>$privilage,'branch'=>$branch,'company'=>$company,'option'=>$option,'centre'=>$centre]);
    	}
    	else
    	{
        	return view('dashboard');
    	}
		
	}
	
	public function getuserDatatable(request $request) 
	{
		$privilege = Auth::user()->previlage;
        $main_id = 3;
       	$sub_id  = 33;
     	$center = Auth::user()->user_branch;
		
      	$option = DB::table('tbl_menu_set_options')
                        ->select('opset_options')
                        ->where('opset_privilege',$privilege)
                        ->where('opset_main_id',$main_id)
                        ->where('opset_sub_id',$sub_id)
                        ->first();    
		
		$limit = ($request->length != '') ? $request->length : 10;
		$offset = ($request->start != '') ? $request->start : 0;
		$search = $request->search['value'];
		
		$order = $request->order;
        $columns = $request->columns;
        $colName = 'user_activity_logs.id';
        $sort = '';
		 if (isset($order[0]['column']) && isset($order[0]['dir'])) {
				$colNo = $order[0]['column'];
				$sort = $order[0]['dir'];
				if (isset($columns[$colNo]['name'])) {
					$colName = $columns[$colNo]['name'];
				}
			}
		
		$user = DB::table('users')->select('users.id as userid','users.user_id','users.username','users.name','users.lname','users.previlage','users.user_branch',
'users.user_email','users.mobile','privilege.privilege_name','tbl_branch.branch_name','b.name as addedbyname')
              ->leftjoin('privilege','privilege.id','users.previlage')
              ->leftjoin('tbl_branch','tbl_branch.branch_id','users.user_branch')
              ->leftjoin('tbl_company','tbl_company.company_id','users.user_company')
			  ->leftjoin('users as b', 'b.id' ,'=','users.added_by')

              ->Where(function($query) use ($search) 
                {
					$query->where('users.name', 'like', $search . '%');
					$query->orWhere('users.user_email', 'like', $search . '%');
					$query->orWhere('users.mobile', 'like', $search . '%');
                });
		
			if($privilege != 2)
			{
				$user->where('users.user_branch', session('application_branch'));
			}
		
		//$user->where('users.user_branch', session('application_branch'));
		
		$user->where('users.status', '0');
		
	   if($colName != '' && $sort != '') {
			  $user->orderBy($colName, $sort);
			} else {
			  $user->orderBy('users.user_id', 'DESC');
       }
		
		$data = ["iTotalDisplayRecords" => $user->count(), "iTotalRecords" => $user->count(), "TotalDisplayRecords" => $limit,"option"=>$option];
		$data['data'] = $user->skip($offset)->take($limit)->get()->toArray();
		
		return response()->json($data);
	}
	
	public function user_view(request $request)
	{
		$data = UsersModel::select('*')
              ->leftjoin('privilege','privilege.id','users.previlage')
              ->leftjoin('tbl_branch','tbl_branch.branch_id','users.user_branch')
              ->where('users.id',$request->id)
              ->first();
			  
		return view ('users::user_view')->with(['data'=>$data]);            
	}
	
	public function common_id_generation($UID) // generate unique IDs
    {
        $user = DB::table('users')->where('status', 0)->orderBy('id', 'desc')->first();
        if (!$user) 
		{
           return 'TT00001';
           //return 'CADD0001';
        } 
		else 
		{
		    preg_match_all('!\d+!', $user->user_id, $matches);
            $digitOnly = $matches[0][0];
            $digitOnly++;
            return 'TT'. (sprintf('%05d' ,$digitOnly));                
        }
    }	
	
	public function add_users(request $request)
	{
		//$user_data = UsersModel::select('user_id')->max('user_id');
		
		/* $res = preg_replace("/[^0-9]/", "", $user_data )+1;
		$id= 'ASAP'.$res ; */
		
		$user_data = UsersModel::select('user_id')->max('user_id');
        $id = $this->common_id_generation($user_data);		
		
		$user_fname = strtolower($request->user_fname);
		$username =  str_replace(' ', '', $user_fname);
		
		// check username already exist in db  //
		if(UsersModel::where('name','=',$request->user_fname)->exists()) 
		{  
			$rand = mt_rand(100,999);
			$username = $username.''.$rand; // already existed user with random number
		}	    	   
		/***********************************/
		
			$data = array(
                'name' => strip_tags($request->user_fname),
                'lname' => strip_tags($request->user_lname),
                'user_email' => strip_tags($request->user_email),
                'mobile' => strip_tags($request->user_mobile),
                'user_perm_address' => strip_tags($request->user_address),
                'previlage' => strip_tags($request->user_privilage),
                'user_company'=>strip_tags($request->user_company),
               // 'user_branch' => strip_tags($request->user_branch),
                'user_designation'	=> strip_tags($request->user_designation),
                'password' => hash::make(strip_tags($request->user_password)),
                'status' => 0,
                'user_ip' => $request->ip(),
                'user_id' => $id,
                'username' => $username,
                'android_token'=>'',
                'android_user_pin'=>0,
				"added_by"=>Auth::user()->id,
            );
			if ($request->hasFile('user_img')) 
			{
				$file = $request->file('user_img');
				//$rules= ['user_img' => "required"];
				$file_name = str::random(30) . '.' . $file->getClientOriginalExtension();
				$path = base_path() . '/public/uploads/user_image';
				$file->move($path, $file_name);
				$data['user_img'] = '/public/uploads/user_image/'.$file_name;
			}
		
			$branchArray = $request->user_branch;
			
			if($request->multiple_branch == 1){
				$multiplebranch = implode(',', $request->user_branch);   
				$data['user_multiple_branch'] = 1;
				$data['user_multiple_branch_id'] = $multiplebranch;
				$data['user_branch'] = $branchArray[0];
			}else{
				$data['user_multiple_branch'] = 0;
				$data['user_multiple_branch_id'] = '';
				$data['user_branch'] = $branchArray;
			}
		
		$validator = Validator::make($request->all(),[
								'user_fname'=>'required|min:3',
								'user_email' =>['required', 
												Rule::unique('users', 'user_email')->where(function ($query) {
													$query->where('status','!=','1');
												}),],
								'user_mobile'=>['required','min:10',
										   Rule::unique('users', 'mobile')->where(function ($query) {
											   $query->where('status','!=','1');
										   }),
										  ],
								'user_privilage'=>'required|numeric',
								//'user_id'=>'required',
								'user_password'=>'required',
								'user_branch' => 'required',
                              ]);
		
		if ($validator->fails()) 
		{
			foreach (array_values($validator->messages()->toArray()) as $msg) 
			{
				$error = implode(' ', $msg) . '<br>';
			}
			return response()->json(['status' => 0, 'msg' => $error]);
		} 
		else 
		{
			$insert = UsersModel::create($data);
			if($insert)
			{
				//Logs & Activity Manager.
				$ip=$request->ip();
				$action = '';
				$user_name=Auth::user()->name;
				$user_id = Auth::user()->id;
				$activity = 'New User Has been Added By '. $user_name.' ';
				$log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
				Core::userActivityAction($log_array);             
				return response()->json(['status' => 1, 'msg' => 'User added successfully!', 'heading' => 'Success']);
			}
			try 
			{
			} 
			catch (\PDOException $e) 
			{
				return Core::log(__CLASS__, __FUNCTION__, $e);
			} 
			catch (\Exception $e) 
			{
				return Core::log(__CLASS__, __FUNCTION__, $e);
			}
		} 
	} 
	
	public function get_user(request $request)
	{
		$id = $request->id;
		$data  = UsersModel::where('id',$id)->first();
		
		return response()->json(['data'=>$data]);
	} 
	
	public function editUserAction(Request $request)
	{  
		$user_fname = strtolower($request->user_fname);
		$username =  str_replace(' ', '', $user_fname);
		
		// check username already exist in db  //
		if(UsersModel::where('name','=',$request->user_fname)->exists()) 
		{  
			$rand = mt_rand(100,999);
			$username = $username.''.$rand; // already existed user with random number
		}	    	   
		/***********************************/
		
		$id = $request->edit_id;
	
			$data = array(
                'name' => strip_tags($request->user_fname),
                'lname' => strip_tags($request->user_lname),
                'user_email' => strip_tags($request->user_email),
                'mobile' => strip_tags($request->user_mobile),
				'user_designation'	=> strip_tags($request->user_designation),
                'user_perm_address' => strip_tags($request->user_address),
                'previlage' => strip_tags($request->user_privilage),
                'user_company'=>strip_tags($request->user_company),
                //'user_branch' => strip_tags($request->user_branch),
                'username' => $username,
                //'password' => $user_password,
                'status' => 0,
                'user_ip' => $request->ip(),
				"edited_by"=>Auth::user()->id,
            );  
		
		if($request->user_password != null)
		{   
			$data['password'] = hash::make(strip_tags($request->user_password));
		}
		
		$branchArray = $request->user_branch;
		if($request->multiple_branch == 1){
			
			$multiplebranch = implode(',', $request->user_branch);   
			$data['user_multiple_branch'] = 1;
			$data['user_multiple_branch_id'] = $multiplebranch;
			$data['user_branch'] = $branchArray[0];
		}else{
			$data['user_multiple_branch'] = 0;
			$data['user_multiple_branch_id'] = '';
			$data['user_branch'] = $branchArray;
		}	
		
		$validator = Validator::make($data,[
                                      'name'=>'required|min:3',
                                      'user_email' =>['required', 
														Rule::unique('users', 'user_email')->where(function ($query) use($id) {
															$query->where('status','!=','1');
															$query->where('id','!=',$id);
														}),],
                                      'mobile'=>['required','min:10',
														Rule::unique('users', 'mobile')->where(function ($query) use($id) {
															$query->where('status','!=','1');
															$query->where('id','!=',$id);
														}),
									  			],
                                      'previlage'=>'required|numeric',
                                      //'password'=>'required',
                                      'user_branch' => 'required|numeric',
                                      ]);
		if ($validator->fails()) 
		{
			foreach (array_values($validator->messages()->toArray()) as $msg) 
			{
				$error = implode(' ', $msg) . '<br>';
			}
			return response()->json(['status' => 0, 'msg' => $error]);
		} 
		else 
		{
			if ($request->hasFile('user_img')) 
			{
				$file = $request->file('user_img');
				//$rules= ['user_img' => "required"];
				$file_name = str::random(30) . '.' . $file->getClientOriginalExtension();
				$path = base_path() . '/public/uploads/user_image';
				$file->move($path, $file_name);
				$data['user_img'] = $file_name;
            }
			
			UsersModel::where('id',$request->edit_id)->update($data);
			//Logs & Activity Manager.
			$ip=$request->ip();
			$action = '';
			$user_name=Auth::user()->name;
			$user_id = Auth::user()->id;
			$activity = 'User of id '.$request->edit_id.' Has been Updated By '. $user_name;
			$log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
			Core::userActivityAction($log_array);    
			
			return response()->json(['status' => 1, 'msg' => 'User Updted successfully!', 'heading' => 'Success']);
		}
		try 
		{
		} 
		catch (\PDOException $e) 
		{
			return Core::log(__CLASS__, __FUNCTION__, $e);
		} 
		catch (\Exception $e) 
		{
			return Core::log(__CLASS__, __FUNCTION__, $e);
		}
	} 
	
	public function deleteUserAction(Request $request)
	{
		$id = $request->id;
		UsersModel::where(['id'=>$id])->update(['status'=>1]);
		//Logs & Activity Manager.
		$ip=$request->ip();
		$action = '';
		$user_name=Auth::user()->name;
		$user_id = Auth::user()->id;
		$activity = 'User of ID : '.$id.' Has been Deleted By '. $user_name;
		$log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
		Core::userActivityAction($log_array);
		
		return response()->json(['status' => 1, 'msg' => 'User Deleted successfully!', 'heading' => 'Success']);
	}
	
	public function get_allbranch()
	{
		$company_id = $request->company_id;
		$branch = DB::table('tbl_branch')
                      ->select('branch_id','branch_name')
                      ->where('branch_status',0)
                      ->where('company_id',$company_id)
                      ->get();
					  
		return response()->json($branch);               
	}
	
	public function resetMyPassword_user(Request $request)
	{
     	 $userId = $request->id;
		
		  //$user_mypass_current = strip_tags($request->user_mypass_current);
		  $user_mypass_new = strip_tags($request->user_mypass_new);
		  $user_mypass_conf = strip_tags($request->user_mypass_conf);
		  $user_mypass_conf1 = hash::make($user_mypass_conf);    	
		
		$old_password=Auth::user()->password;
		
		if (Hash::check($user_mypass_new, $user_mypass_conf1)) 
		{
 			$allow = 1; 
		}
		else
		{
			$allow =0;
		}
	
		$data = array('password'=>hash::make($user_mypass_new));
     
		if(($user_mypass_new == $user_mypass_conf) &&($allow ==1) ) 
		{
			$input = UsersModel::where('id',$userId)->update($data);
			
     		if($input)
			{
            	//Logs & Activity Manager.
                $ip=$request->ip();
                $action = '';
                $user_name=Auth::user()->name;
                $user_id = Auth::user()->id;
                $activity = 'User  Has been Deleted By '. $user_name.' ';
                $log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
                Core::userActivityAction($log_array);
     			return response()->json(['status' => 1, 'msg' => 'Password Reset successfully!', 'heading' => 'Success']);
     		}
		}
		else
		{
			return response()->json(['status' => 0, 'msg' => 'Password Does Not match!', 'heading' => 'Warning']);
		}

     }	
	
	public function getActivityDatatable(Request $request)
	{
		$limit = ($request->length != '') ? $request->length : 10;
		$offset = ($request->start != '') ? $request->start : 0;
		$search = $request->search['value'];
		$order = $request->order;
        $columns = $request->columns;
        $colName = 'user_activity_logs.id';
        $sort = '';
		$privilege = Auth::user()->previlage;
		$user_id = $request->id;
	
		 if (isset($order[0]['column']) && isset($order[0]['dir'])) {
				$colNo = $order[0]['column'];
				$sort = $order[0]['dir'];
				if (isset($columns[$colNo]['name'])) {
					$colName = $columns[$colNo]['name'];
				}
			}

		$query = DB::table('user_activity_logs')
				->select('user_activity_logs.*',DB::raw('DATE_FORMAT(user_activity_logs.created_at, "%d-%m-%Y") as date'),DB::raw('DATE_FORMAT(user_activity_logs.created_at, "%h:%i %p") as time'))
				->where('activity_user_id',$user_id)
			    ->Where(function($query) use ($search) 
					{
						$query->where('user_activity_logs.activity_category', 'like', $search . '%');
						$query->orWhere('user_activity_logs.activity_desc', 'like', $search . '%');
					});
			if ($colName != '' && $sort != '') {
				$query->orderBy($colName, $sort);
			} else {
			   $query->orderBy('id', 'DESC');
			}
			$data = ["iTotalDisplayRecords" => $query->count(), "iTotalRecords" => $query->count(), "TotalDisplayRecords" => $limit];
			$data['data'] = $query->skip($offset)->take($limit)->get()->toArray();

			return response()->json($data);
	}
}
