<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Branch\Models\Branch;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\Core;
use DB;
use Session;
use Auth;


class CompanyController extends Controller
{
	public function formelements()
	{
		return view('company::index');
	}
	public function index()
	{  
		$privilege = Auth::user()->previlage;
		$main_id = 22;
		$sub_id  = 52;
     
		$option = DB::table('tbl_menu_set_options')
                        ->select('opset_options')
                        ->where('opset_privilege',$privilege)
                        ->where('opset_main_id',$main_id)
                        ->where('opset_sub_id',$sub_id)
                        ->first();
		
		$country = DB::table('countries')->select('id','name')->get();
    
		$permission = DB::table('privilege')
                        ->select('alloted_mainmenus','alloted_submenus')
                        ->where('status',0)
                        ->where('id',$privilege)
                        ->get();
                     
		  if(in_array($main_id,json_decode($permission[0]->alloted_mainmenus)) && in_array($sub_id,json_decode($permission[0]->alloted_submenus)))
    	{                    
			return view('company::company_index',['country' => $country,'option'=>$option]);
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
	
	public function add_company(Request $request)
	{
		$unq_id = DB::table('tbl_company')->select('company_unq_id')->orderby('company_id','DESC')->first();
		//dd($unq_id);
		/*if($unq_id)
		{
			$id = ltrim($unq_id->company_unq_id, 'CADD'); 
			$id = (int)$id+1;
			$id ='CADD'.$id;
		}
		else
		{
			$id ='CADD00000'; 
		}*/
		
		
		 if($unq_id == "")
		{ 
			$unq_id = "C00000"; 
		}
		else
		{ 
			$unq_id = $unq_id->company_unq_id;
		}
		$cnt = str_split($unq_id);
		$alph = $cnt[0];
		$num = $cnt[1].$cnt[2].$cnt[3].$cnt[4].$cnt[5];
		$final_num = $alph.$num;
		$num = (int)$num+1;
		$id = $alph.sprintf('%05d',$num);
		//dd($id);
		$data = array(
                'company_ip' => $request->ip(),
                'company_status' => 0,
                'created_at'=>date('Y-m-d'),
                'company_name' => strip_tags($request->company_name),
                'company_code' =>strip_tags($request->company_code),
                'company_person' =>strip_tags($request->company_person),
                'company_mob' =>strip_tags($request->company_mob),
                'company_lan' =>strip_tags($request->company_lan),
                'company_email' => strip_tags($request->company_email),
                'company_web' =>strip_tags($request->company_web),
                'company_address' =>strip_tags($request->company_address),
                // 'branch_gst' =>strip_tags($request->branch_gst),
                //'company_lat' =>strip_tags($request->company_lat),
                //'company_long' =>strip_tags($request->company_long),
                'company_unq_id' => $id,
                'company_country' => strip_tags($request->company_country)  != ''  ?  strip_tags($request->company_country) : 0 ,
                'company_state' =>strip_tags($request->company_state) !=''  ?  strip_tags($request->company_state) : 0,
                'company_city' =>strip_tags($request->company_city)  !=''  ?  strip_tags($request->company_city) : 0,
				"company_addedby"=>Auth::user()->id,
            );
			
			$validator = Validator::make($data,[
                            'company_name' => 'string|required',
                            'company_person' => 'string',
                            'company_mob' => 'numeric',
                            'company_lan' => 'numeric',
                            'company_country' => 'numeric',
                            'company_state' => 'numeric',
                            'company_city' => 'numeric'
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
				if ($request->hasFile('company_logo')) 
				{
					$file = $request->file('company_logo');
					$rules= ['company_logo' => "required"];
					$file_name = str::random(30) . '.' . $file->getClientOriginalExtension();
					$path = base_path() . '/public/uploads/company_logo';
					$file->move($path, $file_name);
					$data['company_logo'] = '/public/uploads/company_logo/'.$file_name;
				}
				else
				{         
					$data['company_logo'] = "";
				}
				DB::table('tbl_company')->insert($data);
				$ip=$request->ip();
				$action = '';
				$user_name=Auth::user()->name;
				$user_id = Auth::user()->id;
				$activity = 'New Company has been added by '. $user_name.'';
				$log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
				Core::userActivityAction($log_array);
				
				return response()->json(['status' => 1, 'msg' => 'Company Created Successfully', 'heading' => 'Success']);
			}
	}
	
	public function get_datatable(Request $request)
	{
		//dd(1);
		$privilege = Auth::user()->previlage;
		//dd($privilege);
		$main_id = 22;
		$sub_id  = 52;
     
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
		

		$area = DB::table('tbl_company')
				->leftjoin('users', 'users.id' ,'=','tbl_company.company_addedby')
				->where('company_status',0)
              	->Where(function($query) use ($search) 
					{
						$query->where('company_name', 'like', $search . '%');
						$query->orwhere('company_person', 'like', $search . '%');
						$query->orwhere('company_code', 'like', $search . '%');
						$query->orwhere('company_mob', 'like', $search . '%');
                    })
			->orderBy($orderColumn, $orderDirection)
			->orderby('company_id','desc');
					
		$data = ["iTotalDisplayRecords" => $area->count(), "iTotalRecords" => $area->count(), "TotalDisplayRecords" => $limit,'option'=>$option];
		$dataMod = $area->skip($offset)->take($limit)->get();
		$data['data'] = $dataMod->toArray();
		//dd($data);
		return response()->json($data);
	}
	
	public function get_details(request $request)
    {
		$data = DB::table('tbl_company')->where('company_id',$request->id)->first();
		return response()->json($data);
    }
	
	public function view_company(request $request)
    {
		$data = DB::table('tbl_company')->select('*','countries.name as country_name','states.name as state_name','cities.name as city_name')
                ->leftjoin('countries','countries.id','=','tbl_company.company_country')
                ->leftjoin('states','states.id','=','tbl_company.company_state')
                ->leftjoin('cities','cities.id','=','tbl_company.company_city')
                ->where('company_id',$request->id)->first();
				
		return view('company::view_details',['data'=>$data]);
    }
	
	public function edit_company(request $request)
    {
		$data = array(
                'company_name' => strip_tags($request->company_name),
                'company_code' =>strip_tags($request->company_code),
                'company_person' =>strip_tags($request->company_person),
                'company_mob' =>strip_tags($request->company_mob),
                'company_lan' =>strip_tags($request->company_lan),
                'company_email' => strip_tags($request->company_email),
                'company_web' =>strip_tags($request->company_web),
                'company_address' =>strip_tags($request->company_address),
                // 'bra_gst' =>strip_tags($request->branch_gst),
                //'company_lat' =>strip_tags($request->company_lat),
                //'company_long' =>strip_tags($request->company_long),
                'company_country' => strip_tags($request->company_country)  != ''  ?  strip_tags($request->company_country) : 0 ,
                'company_state' =>strip_tags($request->company_state) !=''  ?  strip_tags($request->company_state) : 0,
                'company_city' =>strip_tags($request->company_city)  !=''  ?  strip_tags($request->company_city) : 0,
				"company_addedby"=>Auth::user()->id,
            );
			
		$validator = Validator::make($data,[
                                        'company_name' => 'string|required',
                                        'company_person' => 'string',
                                        'company_mob' => 'numeric',
                                        'company_lan' => 'numeric',
                                        'company_country' => 'numeric',
                                        'company_state' => 'numeric',
                                        'company_city' => 'numeric'
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
			if ($request->hasFile('company_logo')) 
			{
				$file = $request->file('company_logo');
				$rules= ['company_logo' => "required"];
				$file_name = str::random(30) . '.' . $file->getClientOriginalExtension();
				$path = base_path() . '/public/uploads/company_logo';
				$file->move($path, $file_name);
				$data['company_logo'] = '/public/uploads/company_logo/'.$file_name;
            }
			
			DB::table('tbl_company')->where('company_id',$request->edit_id)->update($data);
			$ip=$request->ip();
			$action = '';
			$user_name=Auth::user()->name;
			$user_id = Auth::user()->id;
			$activity = 'Company Details has been updated by '. $user_name.'';
			$log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
			Core::userActivityAction($log_array);
			return response()->json(['status' => 1, 'msg' => 'Company Updated Successfully', 'heading' => 'Success']);
        }
    }
	
	public function delete_company(request $request)
    { 
		DB::table('tbl_company')->where('company_id',$request->id)->update(['company_ip'=>$request->ip(),'company_status' => 1]);
		$ip=$request->ip();
		$action = '';
		$user_name=Auth::user()->name;
		$user_id = Auth::user()->id;
		$activity = 'Company has been deleted by '. $user_name.'';
		$log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
		Core::userActivityAction($log_array);
		
		return response()->json(['status' => 1, 'msg' => 'Company Deleted Successfully', 'heading' => 'Success']);
    }
	
    public function setBranch(request $request)
    {   
        $company_id = input::get('id');
        if($company_id=='')
        {
            $company_id = input::get('company_id');
        }
		//  $branch_id = input::get('branch_id');
        Session::put('company_id', $company_id);
		//   Session::put('branch_id', $branch_id);
        $branches = DB::table('tbl_branch')
                    ->where('branch_status',0)
                    ->where('company_id',$company_id)
                    ->get();
					
        return  $branches;
    }
	
	public function set_branch(Request $request)
    {
        echo $branch = $request->id;
        return session(['application_branch' =>$branch ]);
    }  
	
}
