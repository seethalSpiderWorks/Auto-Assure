<?php

namespace Modules\Branch\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Branch\Models\BranchModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\Core;
use DB;
use Auth;
use Session;

class BranchController extends Controller
{
	public function index()
	{
		$privilege = Auth::user()->previlage;
		$main_id = 22;
		$sub_id  = 53;
     
		$option = DB::table('tbl_menu_set_options')
                        ->select('opset_options')
                        ->where('opset_privilege',$privilege)
                        ->where('opset_main_id',$main_id)
                        ->where('opset_sub_id',$sub_id)
                        ->first();  
		
		$country = DB::table('countries')
        	->select('id','name')
        	->get();
		
		$state = DB::table('tbl_state')
        	->select('state_id','state_name')
			->where('state_status',0)
        	->get();
		
		$district = DB::table('tbl_district')
        	->select('district_id','district_name','district_state_id')
			->where('district_status',0)
        	->get();
			
		$company = DB::table('tbl_company')
            ->select('company_id','company_name')
            ->where('company_status',0)
            ->get();
    
		$permission = DB::table('privilege')
                        ->select('alloted_mainmenus','alloted_submenus')
                        ->where('status',0)
                        ->where('id',$privilege)
                        ->get();
                     
		if(in_array($main_id,json_decode($permission[0]->alloted_mainmenus)) && in_array($sub_id,json_decode($permission[0]->alloted_submenus)))
		{                    
			return view('branch::index',['country' => $country,'state'=>$state,'company'=>$company,'district'=>$district,'option'=>$option]);
		}
		else
		{
			return view('dashboard');
		}	
	}
	
	
	
	public function get_state(Request $Request)
	{
		$data = DB::table('states')
				->where('country_id',$Request->country)
				->pluck('name','id');
		return $data;
	}
		
	public function get_district(Request $Request)
	{
		$data = DB::table('tbl_district')
				->where('district_state_id',$Request->state)
				->pluck('district_name','district_id');
		
		return $data;
	}
	
	public function get_city(Request $Request)
	{
		$data = DB::table('cities')
				->where('state_id',$Request->state)
				->pluck('name','id');
		return $data;
	}
		
	public function add_branch(Request $request)
	{
		$unq_id = BranchModel::select('branch_unq_id')->orderby('branch_id','DESC')->first();
		if($unq_id)
		{
			$id = ltrim($unq_id->branch_unq_id, 'AUTOAS'); 
			$id = (int)$id+1;
			$id ='AUTOAS'.$id;
		}
		else
		{
			$id ='AUTOAS100000'; 
		}
		
		$data = array(
                'branch_ip' => $request->ip(),
                'branch_status' => 0,
				'branch_added_by' => Auth::user()->id,
                'company_id'=>$request->company_i,
                'branch_name' => strip_tags($request->branch_name),
                'branch_code' =>strip_tags($request->branch_code),
                'branch_person' =>strip_tags($request->branch_person),
                'branch_mob' =>strip_tags($request->branch_mob),
                'branch_lan' =>strip_tags($request->branch_lan),
                'branch_email' => strip_tags($request->branch_email),
                'branch_web' =>strip_tags($request->branch_web),
                'branch_address' =>strip_tags($request->branch_address),
				'branch_whatsapp' =>strip_tags($request->branch_whatsapp),
                'branch_gmb_link' =>strip_tags($request->branch_gmb_link),
                'branch_gmb_id' =>strip_tags($request->branch_gmb_id),
				'branch_pincode' =>strip_tags($request->branch_pincode),
                //'branch_lat' =>strip_tags($request->branch_lat),
                //'branch_long' =>strip_tags($request->branch_long),
                'branch_unq_id' => $id,
                'branch_country' => strip_tags($request->branch_country)  != ''  ?  strip_tags($request->branch_country) : 0 ,
                'branch_state' =>strip_tags($request->branch_state) !=''  ?  strip_tags($request->branch_state) : 0,
			    'branch_city' =>strip_tags($request->branch_district)  !=''  ?  strip_tags($request->branch_district) : 0,
               );
			   
		$validator = Validator::make($data,[
                                        'branch_name' => 'string|required',
                						'branch_person' => 'string',
                						'branch_mob' => 'numeric',
                						'branch_lan' => 'numeric',
										'branch_whatsapp'=>'numeric',
                						'branch_country' => 'numeric',
                						'branch_state' => 'numeric',
										'branch_district'=> 'numeric',
                						//'branch_city' => 'numeric',
										'branch_pincode'=>'numeric'
                                       ]
                                	);
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
			/*if ($request->hasFile('branch_logo')) 
            {
				$file = $request->file('branch_logo');
				$rules= ['branch_logo' => "required"];
				$file_name = str::random(30) . '.' . $file->getClientOriginalExtension();
				$path = base_path() . '/public/uploads/branch_logo';
				$file->move($path, $file_name);
				$data['branch_logo'] = '/public/uploads/branch_logo/'.$file_name;
            }
			else
            {         
				$data['branch_logo'] = "";
            } */
			
			BranchModel::insert($data);
			$ip=$request->ip();
			$action = '';
			$user_name=Auth::user()->name;
			$user_id = Auth::user()->id;
			$activity = 'New Branch has been added by '. $user_name.'';
			$log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
			Core::userActivityAction($log_array);
			return response()->json(['status' => 1, 'msg' => 'Branch Created Successfully', 'heading' => 'Success']);
        }
	}
	
	public function get_datatable(Request $request)
	{
        $privilege = Auth::user()->previlage;
		$main_id = 22;
		$sub_id  = 53;
     
		$option = DB::table('tbl_menu_set_options')
                        ->select('opset_options')
                        ->where('opset_privilege',$privilege)
                        ->where('opset_main_id',$main_id)
                        ->where('opset_sub_id',$sub_id)
                        ->first(); 
                        
		$limit = ($request->length != '') ? $request->length : 10;
		$offset = ($request->start != '') ? $request->start : 0;
		$search = $request->search['value'];
		$orderColumnIndex = $request->order[0]['column']; 
        $orderDirection = $request->order[0]['dir']; 
		$columns = $request->columns;
		$orderColumn = $columns[$orderColumnIndex]['name'];
		
		$area = BranchModel::select('tbl_branch.*','users.name')
				->where('branch_status',0)
				->join('users','users.id','=','tbl_branch.branch_added_by')
             	->Where(function($query) use ($search) 
                	{
						$query->where('branch_name', 'like', $search . '%');
						$query->orwhere('branch_person', 'like', $search . '%');
						$query->orwhere('branch_code', 'like', $search . '%');
						$query->orwhere('branch_mob', 'like', $search . '%');
                    });
			
		   if(Auth::user()->previlage != 2)
           {
				$area->where('branch_id',session('application_branch'));
		   }
			$area->orderBy($orderColumn, $orderDirection);
			$area->orderby('branch_id','desc');
					
		$data = ["iTotalDisplayRecords" => $area->count(), "iTotalRecords" => $area->count(), "TotalDisplayRecords" => $limit,'option'=> $option];
		$dataMod = $area->skip($offset)->take($limit)->get();
		$data['data'] = $dataMod->toArray();
		
		return response()->json($data);
	}
	
	public function get_details(request $request)
    {
		$data['company'] = DB::table('tbl_company')
						->select('company_id','company_name')
						->where('company_status',0)
						->get();
		$data['data'] = BranchModel::where('branch_id',$request->id)->first();
		return $data;
    }
	
	public function view_branch(request $request)
    {
		$data = BranchModel::select('*','countries.name as country_name','states.name as state_name','tbl_district.district_name as district_name')
                ->leftjoin('countries','countries.id','=','tbl_branch.branch_country')
                ->leftjoin('states','states.id','=','tbl_branch.branch_state')
				->leftjoin('tbl_district','tbl_district.district_id','=','tbl_branch.branch_city')
                //->leftjoin('cities','cities.id','=','tbl_branch.branch_city')
                ->where('branch_id',$request->id)->first();
		return view('branch::view_details',['data'=>$data]);
    }
	
	public function edit_branch(request $request)
    {
		$data = array(
                'branch_ip' => $request->ip(),
                'branch_name' => strip_tags($request->branch_name),
                'branch_code' =>strip_tags($request->branch_code),
                'branch_person' =>strip_tags($request->branch_person),
                'branch_mob' =>strip_tags($request->branch_mob),
                'branch_lan' =>strip_tags($request->branch_lan),
                'branch_email' => strip_tags($request->branch_email),
                'branch_web' =>strip_tags($request->branch_web),
                'branch_address' =>strip_tags($request->branch_address),
				'branch_pincode' =>strip_tags($request->branch_pincode),
                 
				'branch_whatsapp' =>strip_tags($request->branch_whatsapp),
                'branch_gmb_link' =>strip_tags($request->branch_gmb_link),
                'branch_gmb_id' =>strip_tags($request->branch_gmb_id),
                //'branch_country' => strip_tags($request->branch_country),
                //'branch_state' =>strip_tags($request->branch_state),
				//'branch_city' =>strip_tags($request->branch_district),
				'branch_updated_by' => Auth::user()->id,
               );
			   
		$validator = Validator::make($data,[
                                        'branch_name' => 'string|required',
                                        'branch_person' => 'string',
                                        'branch_mob' => 'numeric',
                                        'branch_lan' => 'numeric',
										'branch_whatsapp'=>'numeric',
                                        'branch_country' => 'numeric',
                                        'branch_state' => 'numeric',
                                        'branch_city' => 'numeric',
										'branch_pincode'=>'numeric'
                                       ]
                                    );
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
			/* if ($request->hasFile('branch_logo')) 
            {
				$file = $request->file('branch_logo');
				$rules= ['branch_logo' => "required"];
				$file_name = str::random(30) . '.' . $file->getClientOriginalExtension();
				$path = base_path() . '/public/uploads/branch_logo';
				$file->move($path, $file_name);
				$data['branch_logo'] = '/public/uploads/branch_logo/'.$file_name;
            } */
			
			BranchModel::where('branch_id',$request->edit_id)->update($data);
			$ip=$request->ip();
			$action = '';
			$user_name=Auth::user()->name;
			$user_id = Auth::user()->id;
			$activity = 'Branch Details has been updated by '. $user_name.'';
			$log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
			Core::userActivityAction($log_array);
			return response()->json(['status' => 1, 'msg' => 'Branch Updated Successfully', 'heading' => 'Success']);
        }
    }
	
	public function delete_branch(request $request)
    { 
		BranchModel::where('branch_id',$request->id)->update(['branch_ip'=>$request->ip(),'branch_status' => 1]);
		$ip=$request->ip();
		$action = '';
		$user_name=Auth::user()->name;
		$user_id = Auth::user()->id;
		$activity = 'Branch has been deleted by '. $user_name.'';
		$log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
		Core::userActivityAction($log_array);
		return response()->json(['status' => 1, 'msg' => 'Branch Deleted Successfully', 'heading' => 'Success']);
    }
	
    public function get_branch()
    {
        $branch_id = input::get('branch_id');
        Session::put('branch_id', $branch_id);
    }
}
