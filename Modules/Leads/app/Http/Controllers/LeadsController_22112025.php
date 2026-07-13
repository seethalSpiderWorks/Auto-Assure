<?php

namespace Modules\Leads\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Core;
use Modules\Leads\Models\RegistrationModel;
use Modules\Leads\Models\LeadsModel;
use Modules\Leads\Models\FollowupModel;
 
use DataTables,Auth,DB;

class LeadsController extends Controller
{
	public function index(Request $request)
    {
		$privilege = Auth::user()->previlage;
		$centre  = session('application_branch');
		$main_id = 5;
		$sub_id  = 40;
		$option  = DB::table('tbl_menu_set_options')
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
		
		ini_set('memory_limit', '-1');
        $current = \Route::current()->uri();
        $prev = url()->previous();
         
        if($current!=$prev)
        {
			session()->forget('filter_lead_fdate');
            session()->forget('filter_lead_ldate');
            session()->forget('filter_lead_source');
            session()->forget('filter_lead_status');
		    session()->forget('filter_staff');
		    session()->forget('filter_status');		   
        }
		 
		$countries = DB::table('tbl_country')
                ->pluck('country_name','id')
                ->toArray();
		
		$states = DB::table('states')
				->select('name','id')
				->get();
		
		$district = DB::table('tbl_district')
			    ->where('district_status',0)
				->select('district_name','district_id')
				->get();
		
		$branch = DB::table('tbl_branch')
				->select('branch_name','branch_id')
				->get();
	 
		$sources = DB::table('tbl_source')
                ->where('source_status',0)
                ->pluck('source_name','source_id')
                ->toArray();
				
		$status = DB::table('tbl_followup_type')
                ->where('followup_type_status',0)
                ->pluck('followup_type_name','followup_type_id')
                ->toArray();   
		  
 		
		/******** Assign Staff **********/
		$users = DB::table('users')
				->select(DB::raw('concat(name," ",lname) as name, id'))
				->where('status',0);
		//$users = $users->where('user_branch',session('application_branch'));
		$users = $users->where('previlage',49);  // Technicians only
		$users = $users->pluck('name','id')->toArray(); 
		
	  	/** if(Auth::user()->previlage != 2)
        { 
			$users = $users->where('user_branch',session('application_branch'));
		} **/
		
		/******** Assign Staff **********/
		
		$data = "";
		if($request->has('id'))
     	{
			$id = $request->id;  
			$data = LeadsModel::where('lead_status',0)
					->leftjoin('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id')
					->leftjoin('tbl_branch','tbl_branch.branch_id','tbl_basic_registration.breg_branch_id')
					->leftjoin('tbl_source','tbl_source.source_id','lead_source')
					->leftjoin('tbl_lead_package','tbl_lead_package.lead_pack_lead_id','lead_id')
					->where('tbl_lead.lead_id',$id)
					->orderBy('lead_id','desc')
					->first(); 
		}
	 
		if(in_array($main_id,json_decode($permission[0]->alloted_mainmenus)) && in_array($sub_id,json_decode($permission[0]->alloted_submenus)))
    	{   		
        	return view('leads::index',compact('countries','sources','users','states','branch','status','option','district', 'data'));
		}
		else
    	{
        	return view('dashboard');
    	}	  
    }
	
	/************** INSERT START **************/
	public function basicRegistrtaion(Request $request)
    {   
		$branch_state = $request->branch_state;
	 
        $rules  = ['mob'    => 'required|numeric',
                 //'email'  => 'required|email',
                   'fname'  => 'required|string',
                   'formtype'  => 'required',
                ];
         
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) 
        {
            return redirect()->back()->withInput()->with('error', 'Validation Error!')->withErrors($validator);
        } 
        else 
        {
            $registrtaion_id = $request->reg_id;
            $mobilecodedata = $request->mobilecodedata;
    	    $country = explode("+", $mobilecodedata);
    	   
    	    //$country = $country[1];
			$country = 974;
			$breg_state = 19;
			
            if($registrtaion_id!='')
            { 
				$basic_data =  ['breg_ip'      => $request->ip(),
								'breg_fname'   => $request->fname,
								'breg_fname_ar'=> $request->fname_ar,
								'breg_mob'     => $request->mob,
								'breg_email'   => $request->email,
								'breg_message' => $request->message,
								'breg_district'=> $request->company_city,
								'breg_place'   => $request->breg_place,
                            ];
                RegistrationModel::where('breg_id',$registrtaion_id)->update($basic_data);
                $activity = 'Basic Reg ID '.$registrtaion_id.' Has been updated  By '.Auth::user()->name.' ';
            }
			else
            {
                $basic_data =  ['breg_ip'       => $request->ip(),
								'breg_status'   => 0,
								'breg_date'     => date('Y-m-d'),
								'breg_addedby'  => Auth::user()->id,
								'breg_fname'    => $request->fname,
								'breg_fname_ar' => $request->fname_ar,
								'breg_mob'      => $request->mob,
								'breg_email'    => $request->email,		
								'breg_message'  => $request->message,
								'breg_branch_id'=> $request->centre,
								'breg_place'    => $request->breg_place,								
 							];
							
				$basic_arr = RegistrationModel::create($basic_data);
                $registrtaion_id = $basic_arr->id;
                $activity = 'New Basic Reg ID '.$registrtaion_id.' Has been added By '.Auth::user()->name.' ';
			}
             
			/************************ Insert into lead  ***********************/                    
			$regdata = DB::table('tbl_lead_followup')
					->select('followup_current_status')
					->where('followup_reg_id',$registrtaion_id)
					->where('followup_status',0)
					->where('followup_branch_id',session('application_branch'))
					->get();
			/************************ Admin add lead ***********************/
			
			$makeArray  = $request->make;     
			$make = $request->make;
			/*if($makeArray)
			{
				$make = implode(',', $request->make);   
			}
			else
			{
				$make = '';
			}*/
			
			$modelArray = $request->model;
			if($modelArray)
			{
				$model = implode(',', $request->model);
			}
			else
			{
				$model = '';
			}
				if(count($regdata)>0)
				{               
					$leadolddatass = LeadsModel::where('lead_reg_id',$registrtaion_id)
							->where('lead_status',0)
							->latest()->first();
					
					$lead = ['lead_ip'       => $request->ip(),
							'lead_added_by'  => Auth::user()->id,
							'lead_date'      => date('Y-m-d'),
							'lead_datetime'  => date('Y-m-d H:i:s'),
							'lead_date_on'   => date('Y-m-d'),
							'lead_time_on'   => date('H:i:s'),
							'lead_reg_id'    => $registrtaion_id,
							'lead_source'    => $request->source,//CRM
							'lead_assigned_status' => 'New',
							'lead_followup_type'   => '7',
							'lead_followupcreated' => '',                    
							'lead_assigned_users'  => '',
							'lead_branch_id'       => $request->centre,
							
							'lead_make'     => $make,					
							'lead_model'    => $model,					
							'lead_year'     => $request->year,
							'lead_vehicle_plate_no' => $request->plate_no,		
							
							'lead_color'         => $request->color,					
							'lead_color_ar'      => $request->color_ar,					
							'lead_seller_name'   => $request->sellername,					
							'lead_seller_name_ar'=> $request->sellername_ar,					
							'lead_seller_mobile' => $request->sellermobile,					
							'lead_location'      => $request->location,					
							'lead_your_mobile'   => $request->yourmobile,					
							'lead_add_details'   => $request->additionaldet,					
							'lead_form_type'     => $request->formtype, //1,
							
							'lead_year_from' => $request->yearfrom,					
							'lead_year_to'   => $request->yearto,					
							'lead_budget'    => $request->budget,
 							'lead_know_more' => $request->knowmore,	
							
							/**	'lead_assigned_status' => $leadolddatass->lead_assigned_status,
							'lead_followup_type'   => $leadolddatass->lead_followup_type,
							'lead_followupcreated' => $leadolddatass->lead_followupcreated,                    
							'lead_assigned_users'  => $leadolddatass->lead_assigned_users, **/
							//'lead_branch_id'     => session('application_branch'),
						];
					 
					$res = LeadsModel::create($lead);
					$lid = $res->id;
					$unq_id = 'LD'.str_pad($lid,5,'0',STR_PAD_LEFT);
					LeadsModel::where('lead_id',$lid)->update(['lead_unq_id'=>$unq_id]);   

					/********** PACKAGE START**********/
					$mode_pay    = $request->payment;
					$package_id  = $request->lpackage;  // package ID // lead pachage for Book inspection
			 
					if($package_id != null || $package_id != '' )
					{
						$packname = DB::table('tbl_package')
							->select('package_name','package_id','package_payable')
							->where('package_status',0)
							->where('package_id',$package_id)
							->first(); 
						$package_name = $packname->package_name; //package Name
						$package_pay  = $packname->package_payable; //package Name
					
						$packagedata = ['lead_pack_ip'      => $request->ip(),
										'lead_pack_date'    => date('Y-m-d'),
										'lead_pack_status'  => 0,
										'lead_pack_reg_id'  => $registrtaion_id,
										'lead_pack_lead_id' => $lid,
										'lead_pack_name_id' => $package_id, 
										'lead_pack_name'    => $package_name, 
										'lead_amount_pay'   => $package_pay,
						];
						
						$lead_pakId = DB::table('tbl_lead_package')->insertGetId($packagedata);
						
						$idColumn = $lead_pakId;
						$dateCode = date('ym');
						$unq_id   = 'BI'.$dateCode. str_pad($idColumn, 5, '0', STR_PAD_LEFT);
								
						$payment = ['lead_mode_pay'     => $mode_pay,
									'lead_pack_refe_id' => $unq_id    ];
						$updatePack = DB::table('tbl_lead_package')
							->select('lead_pack_id','lead_mode_pay')
							->where('lead_pack_id', $lead_pakId)
							->update($payment); 
					}
					/********** PACKAGE END **********/
				}
				else
				{
					$lead =	['lead_ip'      => $request->ip(),
							'lead_added_by' => Auth::user()->id,
							'lead_date'     => date('Y-m-d'),
							'lead_datetime' => date('Y-m-d H:i:s'),
							'lead_date_on'  => date('Y-m-d'),
							'lead_time_on'  => date('H:i:s'),
							'lead_reg_id'   => $registrtaion_id,
							'lead_source'   => $request->source,//CRM
							'lead_branch_id'=> $request->centre,
							'lead_assigned_status' => 'New',
							'lead_followup_type'   => '7',
							
							'lead_make'     => $make,					
							'lead_model'    => $model,					
							'lead_year'     => $request->year,	
							'lead_vehicle_plate_no' => $request->plate_no,		
							
							'lead_color'         => $request->color,					
							'lead_color_ar'      => $request->color_ar,					
							'lead_seller_name'   => $request->sellername,					
							'lead_seller_name_ar'=> $request->sellername_ar,					
							'lead_seller_mobile' => $request->sellermobile,					
							'lead_location'      => $request->location,					
							'lead_your_mobile'   => $request->yourmobile,					
							'lead_add_details'   => $request->additionaldet,					
							'lead_form_type'     => $request->formtype, //1,
							
							'lead_year_from' => $request->yearfrom,					
							'lead_year_to'   => $request->yearto,					
							'lead_budget'    => $request->budget,
 							'lead_know_more' => $request->knowmore,
							//'lead_assigned_users'=> '',
							//'lead_branch_id'=>session('application_branch'),
						];
            
					$res = LeadsModel::create($lead);
					$lid = $res->id;
					$unq_id = 'LD'.str_pad($lid,5,'0',STR_PAD_LEFT);
					LeadsModel::where('lead_id',$lid)->update(['lead_unq_id'=>$unq_id]);  

					/********** PACKAGE START**********/
					$mode_pay    = $request->payment;
					$package_id  = $request->lpackage;  // package ID // lead pachage for Book inspection
			 
					if($package_id != null || $package_id != '' )
					{
						$packname = DB::table('tbl_package')
							->select('package_name','package_id','package_payable')
							->where('package_status',0)
							->where('package_id',$package_id)
							->first(); 
						$package_name = $packname->package_name; //package Name
						$package_pay  = $packname->package_payable; //package Name
					
						$packagedata = ['lead_pack_ip'      => $request->ip(),
										'lead_pack_date'    => date('Y-m-d'),
										'lead_pack_status'  => 0,
										'lead_pack_reg_id'  => $registrtaion_id,
										'lead_pack_lead_id' => $lid,
										'lead_pack_name_id' => $package_id, 
										'lead_pack_name'    => $package_name, 
										'lead_amount_pay'   => $package_pay,
						];
						
						$lead_pakId = DB::table('tbl_lead_package')->insertGetId($packagedata);
						 
						$idColumn = $lead_pakId; // lead_package id
						$dateCode = date('ym');
						$unq_id   = 'BI'.$dateCode. str_pad($idColumn, 5, '0', STR_PAD_LEFT);
								
						$payment = ['lead_mode_pay'     => $mode_pay,
									'lead_pack_refe_id' => $unq_id    ];
						$updatePack = DB::table('tbl_lead_package')
							->select('lead_pack_id','lead_mode_pay')
							->where('lead_pack_id', $lead_pakId)
							->update($payment); 
					}
					/********** PACKAGE END **********/
					
					
				}
					
			/*************************************************************/	
			
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $activity= "New Lead ".$unq_id." Has been added by ".$user_name;
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity];
            Core::userActivityAction($log_array);
            return response()->json(['status'=>1,'reg_id'=>$registrtaion_id,'lead_id'=>$lid]);
        }
    }
	
	public function datatable(Request $request)
	{
		ini_set('memory_limit', '-1');        
        $current_route = \Route::current()->uri();
        $today = date('Y-m-d');
		
		$privilege = Auth::user()->previlage;
		$main_id = 5;
		$sub_id  = 40;
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

		$limit   = ($request->length != '') ? $request->length : 10;
		$offset  = ($request->start != '') ? $request->start : 0;
		$search  = $request->search['value'];
		$order   = $request->order;
        $columns = $request->columns;
        $colName = 'tbl_lead.lead_id';
        $sort = 'DESC';
		$privilege = Auth::user()->previlage;
		
   		if (isset($order[0]['column']) && isset($order[0]['dir'])) 
		{
            $colNo = $order[0]['column'];
            $sort = $order[0]['dir'];
            if (isset($columns[$colNo]['name'])) 
			{
                $colName = $columns[$colNo]['name'];
            }
        }
		
		$data = DB::table('tbl_lead')
			->select('tbl_source.source_name','users.name','tbl_basic_registration.breg_id','tbl_basic_registration.breg_email','tbl_basic_registration.breg_date','tbl_basic_registration.breg_fname','tbl_basic_registration.breg_mob','tbl_basic_registration.breg_mob_code','tbl_basic_registration.breg_mob_code','tbl_lead.lead_date','tbl_lead.lead_reg_id','tbl_lead.lead_source','tbl_lead.lead_assigned_users','tbl_lead.lead_assigned_status','tbl_lead.lead_followupcreated','tbl_lead.lead_datetime','tbl_lead.lead_unq_id','tbl_lead.lead_id','tbl_lead_followup.followup_date','tbl_lead_followup.next_followup_date','lead_pack_name','tbl_lead.lead_form_type','tbl_lead.make_model_year')                 
			->where('lead_status',0)
			->leftjoin('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id')
			->leftjoin('tbl_source','tbl_source.source_id','lead_source')
			->leftjoin('users','users.id','lead_assigned_users')
			->leftjoin('tbl_lead_followup','tbl_lead_followup.followup_lead_id','lead_id')
			->leftjoin('tbl_lead_package','tbl_lead_package.lead_pack_lead_id','lead_id')
			->groupBy('tbl_lead.lead_id')
			->Where(function($query) use ($search) 
                {
					$query->where('users.name', 'like', $search . '%');
					$query->orWhere('tbl_basic_registration.breg_fname', 'like', $search . '%');
					$query->orWhere('tbl_basic_registration.breg_mob', 'like', $search . '%');
					$query->orWhere('tbl_lead.lead_assigned_status', 'like', $search . '%');
					$query->orWhere('tbl_source.source_name', 'like', $search . '%');
					$query->orWhere('tbl_lead.lead_unq_id', 'like', $search . '%');
					$query->orWhere('tbl_lead.lead_form_type', 'like', $search . '%');  
                });
		
			if($current_route=="leads/get-list")
			{
				$manage_options = DB::table('tbl_menu_set_options')
							->select('opset_options')
                       		->where('opset_status', '0')
                      		->where('opset_privilege', '=', Auth::user()->previlage)
                       		->where('opset_main_id', '=',5)
                       		->where('opset_sub_id', '=', 40)
                       		->orderBy('opset_id')->first();
				
				$data->where('lead_date','=',date('Y-m-d'));
				
             	$user_previlage = Auth::user()->previlage;
				//$data->where('lead_branch_id',session('application_branch'));
				
				if($user_previlage != 1 && $user_previlage != 2)
				{
					$data->where('lead_branch_id',session('application_branch'));
					$data->where('lead_added_by',Auth::user()->id);
				}  
				/*if(Auth::user()->previlage == 48 )
        		{
					$data->where('lead_added_by',Auth::user()->id);
				}*/
				
			}
			
			else if($current_route=="myleads/get-list")
			{            
				$manage_options = DB::table('tbl_menu_set_options')
							   ->select('opset_options')
							   ->where('opset_status', '0')
							   ->where('opset_privilege', '=', Auth::user()->previlage)
							   ->where('opset_main_id', '=',5)
							   ->where('opset_sub_id', '=',39)
							   ->orderBy('opset_id')->first();
				
				$user_previlage = Auth::user()->previlage;
				
				if($user_previlage != 1 && $user_previlage != 2)
				{
					//$data->where('lead_branch_id',session('application_branch'));
					$data->where('lead_added_by',Auth::user()->id);
				}  
				
				if(Auth::user()->previlage == 48 )
        		{
					$data->where('lead_added_by',Auth::user()->id);
				}
				//$data->where('lead_assigned_users',Auth::user()->id);
              
				if(session('filter_lead_fdate') && session('filter_lead_ldate'))
                {
                  $data->whereBetween('lead_date',[session('filter_lead_fdate'),session('filter_lead_ldate')]);
                }
                else if(session('filter_lead_fdate'))
                {
                    /** $data->where('lead_date','>=',session('filter_lead_fdate'));  **/
					$data->where('lead_date','=',session('filter_lead_fdate'));
                }
                else if(session('filter_lead_ldate'))
                {
                    $data->where('lead_date','<=',session('filter_lead_ldate'));
                }
			}
			else
			{             
				if(session('filter_lead_fdate') && session('filter_lead_ldate'))
				{
					$data->whereBetween('lead_date',[session('filter_lead_fdate'),session('filter_lead_ldate')]);
				}
				else if(session('filter_lead_fdate'))
				{
					$data->where('lead_date','=',session('filter_lead_fdate'));
				}
				else if(session('filter_lead_ldate'))
				{
					$data->where('lead_date','<=',session('filter_lead_ldate'));
				}           
			}
		
			if(session('filter_lead_status'))
			{
			}
			if(session('filter_lead_source'))
			{
				$data->where('lead_source',session('filter_lead_source'));
			}

			if(session('filter_staff'))
			{       
				$data->where('lead_assigned_users',session('filter_staff'));

				$data->Where('lead_status',0);             
			}
			
			if(session('filter_status'))
			{            
				if(session('filter_status')=="New")
				{               
					$unstatus = " ";
					$data->where('lead_assigned_status',$unstatus);
				}           
				else
				{               
					$data->where('lead_assigned_status',session('filter_status')); 
				}
			}
			
			 
		$data->where('tbl_lead.lead_status', '0');
		if ($colName != '' && $sort != '') 
		{
			$data = $data->orderBy($colName, $sort);
		} 
		else 
		{
			$data = $data->orderBy('tbl_lead.lead_id','desc');
		}
	
		$datas = ["iTotalDisplayRecords" => count($data->get()), "iTotalRecords" => count($data->get()), "TotalDisplayRecords" => $limit,'option'=>$option];
		$dataMod = $data->skip($offset)->take($limit)->get();  
		$datas['data'] = $dataMod->toArray(); //dd($datas);
		return response()->json($datas);
    }
	
	public function getleadsdata(Request $request)
    {
        $id = $request->id;
		
		$data = LeadsModel::where('lead_status',0)
                ->leftjoin('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id')
                ->leftjoin('tbl_source','tbl_source.source_id','lead_source')
                ->leftjoin('tbl_lead_package','tbl_lead_package.lead_pack_lead_id','lead_id')
                ->where('tbl_lead.lead_id',$id)
                ->orderBy('lead_id','desc')
                ->first();  // dd($data);
      
		$flag = DB::table("tbl_country")->where('id')->first();
		if($flag)
		{
			$flag1 = strtolower($flag->country_code);
			$country_code = strtolower($flag->country_code);
		}
		else
		{
			$flag1 = "";
			$country_code ="";
		}
             
        return response()->json(['data'=>$data,'flag'=>$flag1,'country_code'=>$country_code]);
    }
	
	public function update_basic_info(Request $request)
    {
		$rules  = ['mob'   => 'required|numeric',
                 //'email' => 'required|email',
                   'fname' => 'required|string',
                ];
         
		$validator = Validator::make($request->all(),$rules);
		if ($validator->fails()) 
        {
			return redirect()->back()->withInput()->with('error', 'Validation Error!')->withErrors($validator);
        } 
		else 
		{
			$registrtaion_id = $request->reg_id;
             
            $mobilecodedata = $request->mobilecodedata;
    	    $country = explode("+", $mobilecodedata);
    	    
			$country = 91;
    	    $breg_state = 19;
    	     
			if($registrtaion_id!='')
            {
				$basic_data = [	'breg_ip'        => $request->ip(),
								'breg_updatedby' => Auth::user()->id,
								'breg_fname'     => $request->fname,
								'breg_fname_ar'  => $request->fname_ar,
								'breg_mob'       => $request->mob,
								'breg_email'     => $request->email,
								'breg_message'   => $request->message,
								'breg_branch_id' => $request->centre,
								'breg_place'     => $request->breg_place,
								'breg_state'     => $breg_state,
								'breg_district'  => $request->company_city,
								];
                            
				RegistrationModel::where('breg_id',$registrtaion_id)->update($basic_data);
				$activity = 'Basic Reg ID '.$registrtaion_id.' Has been updated By '.Auth::user()->name.' ';
            }
			else
			{
				$basic_data = ['breg_ip'      => $request->ip(),
							   'breg_status'  => 0,
							   'breg_date'    => date('Y-m-d H:i:s'),
							   'breg_addedby' => Auth::user()->id,
							   'breg_fname'   => $request->fname,
							   'breg_fname_ar'=> $request->fname_ar,
							   'breg_mob'     => $request->mob,
							   'breg_email'   => $request->email,
							   'breg_state'   => $breg_state,
							   'breg_district'=> $request->company_city,
							   'breg_place'   => $request->breg_place,
							   'breg_message' => $request->message,
							];
				$basic_arr = RegistrationModel::create($basic_data);
                $registrtaion_id = $basic_arr->id;
                $activity = 'New Basic Reg ID '.$registrtaion_id.' Has been added By '.Auth::user()->name.' ';
			}
			
			
			/**** lead start ****/
			/*$makeArray  = $request->make;     
			if($makeArray)
			{
				$make = implode(',', $request->make);   
			}
			else
			{
				$make = '';
			}140325*/
			$make  = $request->make; 
			
			$modelArray = $request->model;  // dd($modelArray);
			if($modelArray)
			{
				$model = implode(',', $request->model);
			}
			else
			{
				$model = '';
			}

             
			/******** Update into lead Start ********/
            $lead_id = $request->lead_id;
            $res = LeadsModel::where('lead_id', $lead_id)->where('lead_status',0)->first();
           
            $lead = [
                    'lead_ip'        => $request->ip(),
                    'lead_added_by'  => Auth::user()->id,
                    'lead_reg_id'    => $registrtaion_id,
                    'lead_source'    => $request->source,//CRM
					'lead_branch_id' => $request->centre,
					'lead_edited_by' => Auth::user()->id,
					
					'lead_make'      => $make,					
					'lead_model'     => $model,					
					'lead_year'      => $request->year,	
					'lead_vehicle_plate_no' => $request->plate_no,	
					
					'lead_color'         => $request->color,					
					'lead_color_ar'      => $request->color_ar,	
					'lead_seller_name'   => $request->sellername,					
					'lead_seller_name_ar'=> $request->sellername_ar,					
					'lead_seller_mobile' => $request->sellermobile,					
					'lead_location'      => $request->location,					
					'lead_your_mobile'   => $request->yourmobile,					
					'lead_add_details'   => $request->additionaldet,					
					'lead_form_type'     => $request->formtype, //1,
		
					'lead_year_from' => $request->yearfrom,					
					'lead_year_to'   => $request->yearto,					
					'lead_budget'    => $request->budget,
 					'lead_know_more' => $request->knowmore,	
                    //'lead_assigned_users'=>'',
                    ]  ;
           
            $lid = $request->lead_id;
           
            $unq_id = $res->lead_unq_id;
            LeadsModel::where('lead_id',$request->lead_id)->update($lead);
            /******** Update into lead end ********/
			
			/********** Update into PACKAGE START**********/
			$mode_pay    = $request->payment;
			$package_id  = $request->lpackage;  // package ID // lead pachage for Book inspection
			 
			if($package_id != null || $package_id != '' )
			{
				$packname = DB::table('tbl_package')
						->select('package_name','package_id','package_payable')
						->where('package_status',0)
						->where('package_id',$package_id)
						->first(); 
						
				$package_name = $packname->package_name; //package Name
				$package_pay  = $packname->package_payable; //package Name
					
				$packagedata = ['lead_pack_ip'      => $request->ip(),
								'lead_pack_date'    => date('Y-m-d'),
								'lead_pack_status'  => 0,
								'lead_pack_reg_id'  => $registrtaion_id,
								'lead_pack_lead_id' => $lid,
								'lead_pack_name_id' => $package_id, 
								'lead_pack_name'    => $package_name, 
								'lead_amount_pay'   => $package_pay,
								'lead_mode_pay'     => $mode_pay,
							];
				 
				$updatePack = DB::table('tbl_lead_package')
						->select('lead_pack_id','lead_mode_pay')
						->where('lead_pack_lead_id', $lid)
						->update($packagedata); 
			}
					/********** PACKAGE END **********/			
			
            $ip = $request->ip();
			$action = '';
			$user_name = Auth::user()->name;
			$user_id = Auth::user()->id;
			$category = "Update Lead";
			$activity= "New Lead ".$unq_id." Has been Updated by ".$user_name;
			$log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
			Core::userActivityAction($log_array);
                
            return response()->json(['status'=>1,'reg_id'=>$registrtaion_id,'lead_id'=>$lid]);
        }
    }
	
	public function leadDelete(Request $request)
    {
		$id   = $request->id;      
		$lead = LeadsModel::where('lead_id',$id)->first();
        if($lead)
		{
		    $lead_id = $lead->lead_unq_id;
            // LeadsModel::where('lead_reg_id',$id)->update(['lead_status'=>1]);
            LeadsModel::where('lead_id',$id)->update(['lead_status'=>1]);
            FollowupModel::where('followup_lead_id',$id)->update(['followup_status'=>1]);
            
			$ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id  = Auth::user()->id;
            $category = "Delete Lead";
            $activity = 'Lead No '.strip_tags($lead_id).' Has been Deleted By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);
            return response()->json(['heading'=>'Success','text'=>'Lead Deleted!','icon'=>'success']);
        }
		else
		{
		    return response()->json(['heading'=>'Error','text'=>'Lead not found','icon'=>'error']);
        }	
    }
	
	
	public function searchRegistration(Request $request)
    {
        $search = $request->mob;   
		if($search != null) 
		{
			$data = RegistrationModel::where('breg_status',0)
					//->where('breg_branch_id',session('application_branch'))
					->Where(function($query)use($search){
							$query->where('breg_mob',$search);
							$query->orWhere('breg_email',$search);
						});
					//->first();   
			if(Auth::user()->previlage != 2)
			{ 
				$data = $data->where('breg_branch_id',session('application_branch'));
			}
			
			$data = $data->first();   
			
			if($data)
			{
				return response()->json(['status'=>1,'result'=>$data]);
			}
		}
		else
		{ 
			return response()->json(['status'=>0]);
		}
    }
	
	public function get_branch(Request $Request)
	{
		$data = DB::table('tbl_branch')
				->where('branch_city',$Request->branch)
				->pluck('branch_name','branch_id');
		return $data;
	}
	
	public function getStaffAction()
    {
		$previlage = Auth::user()->previlage;
        $staff = array();
        $staffData = DB::table('users')
		   		->select('id as staff_id','name','lname')
		   		->where('status',0);
		$staffData = $staffData->where('previlage',49);  // Technicians only
		if($previlage != 1 && $previlage != 2)
		{ 
			//$staffData = $staffData->where('user_branch',session('application_branch'))
			$staffData = $staffData->where('id',Auth::user()->id);
		}  
		
		$staffData = $staffData->get()->toArray();
   		//$staffData = $staffData->where('user_branch',session('application_branch'));
	    //$staffData = $staffData->get()->toArray();
		/* ->get()->toArray(); */
		return response()->json(['status' => 1, 'result'=>$staffData]);
    }
	
	
	/********************** Lead assign in table start **********************/
	public function assignEnqueryDataAction(Request $request)  
    {
		$lead_id = $request->enq_id;
		$staffId = $request->staff_id;

		$unq_id='';
		$old_staus ='';
		$lead_data = LeadsModel::where('lead_id',$lead_id)->where('lead_status',0)->first();    
		
		if(Auth::user()->previlage != 2)
		{
			$lead_data->where('lead_branch_id',session('application_branch'));
		}
		
		if($lead_data)
		{
			$unq_id = $lead_data->lead_unq_id;
			$old_staus = $lead_data->lead_assigned_status;
		}
		
		$users = DB::table("users")
				->select('name','lname')
				->where('id',$staffId)
				->first();
		
		$fdata = FollowupModel::where('followup_lead_id',$lead_data->lead_id)
				->where('followup_status',0)
				->where('followup_branch_id',session('application_branch'))
				->latest()->first();
				
		$followup_current_status = '';		
		if($fdata)
		{				
			$followup_current_status = $fdata->followup_current_status;
		}
		
		if($request->date == null)	
		{
			$next_followup_date = date('Y-m-d');
		}
		else
		{
			$next_followup_date = $request->date;
		}
			
		if($followup_current_status == '' || $followup_current_status == null)  // if followup status null, Assign
		{
			$data = ['followup_ip'     => $request->ip(),
					'followup_on'      => date('Y-m-d H:i:s'),
					'followup_status'  => 0,
					'followup_created' => Auth::user()->id,
					'followup_date'    => date('Y-m-d'),
					'followup_date_on' => date('Y-m-d'),
					'followup_time_on' => date('H:i:s'),	 
					'followup_branch_id' => auth::user()->user_branch,
					'followup_lead_id'   => $lead_id,
					'followup_remarks'   => "Assigned to Staff ".$users->name." ".$users->lname,
					'next_followup_date' => $next_followup_date,
					'followup_type_id'   => 1, // Assign
					'followup_current_status'   => 'Assign',
					'followup_assigned_users_id'=> $staffId,
					'followup_reg_id'    => $lead_data->lead_reg_id,
					'followup_branch_id' => session('application_branch')
					];
				$res = FollowupModel::create($data);              
		}
		else   // Assign changed to reassign
		{
			$data = ['followup_ip'      => $request->ip(),
					'followup_on'       => date('Y-m-d H:i:s'),
					'followup_status'   => 0,
					'followup_created'  => Auth::user()->id,
					'followup_date'     => date('Y-m-d'),
					'followup_date_on'  => date('Y-m-d'),
					'followup_time_on'  => date('H:i:s'),	 
					'followup_branch_id'=> auth::user()->user_branch,
					'followup_lead_id'  => $lead_id,
					'followup_remarks'  => "Reassigned to Staff ".$users->name." ".$users->lname,
					'next_followup_date'=> $next_followup_date,
					'followup_type_id'  => 2, // Reassign
					'followup_current_status'   => 'Reassign',
					'followup_assigned_users_id'=> $staffId,
					'followup_reg_id'   => $lead_data->lead_reg_id,
					'followup_branch_id'=> session('application_branch'),
					//'joining_date'=>$joining_date,
					];
			$res = FollowupModel::create($data);  
		}
		
		LeadsModel::where('lead_id',$lead_data->lead_id)
				->update(['lead_assigned_users'=>$request->user_id]);   // update lead assign user_id
        	
		if($old_staus == 'New')   // new lead changed to assign
		{
			LeadsModel::where('lead_id',$lead_data->lead_id)
				->update(['lead_assigned_users' => $staffId,
						 'lead_assigned_status' => 'Assign',
						 'lead_followup_type'   => 1,
						 'lead_followupcreated' => Auth::user()->id]);
		}
		else   // assign lead changed to reassign
		{
			LeadsModel::where('lead_id',$lead_data->lead_id)
				->update(['lead_assigned_users' => $staffId,
						  'lead_assigned_status'=> 'Reassign',
						  'lead_followup_type'  => 2,
						  'lead_followupcreated'=> Auth::user()->id]);
		}
       
		$ip = $request->ip();
		$action = '';
		$user_name = Auth::user()->name;
		$user_id = Auth::user()->id;
		$category = "New Followup";
		$activity = 'New Followup for lead # '.strip_tags($unq_id).' Has been Added By '.$user_name.' ';
		$log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
		Core::userActivityAction($log_array);
		
		if($old_staus == 'New')
		{
			return response()->json(['heading'=>'Success','text'=>'Leads Assigned Successfully','icon'=>'success']);
		}
		else
		{
			return response()->json(['heading'=>'Success','text'=>'Leads Reassigned Successfully','icon'=>'success']);
		}
		
    }
	/********************** Lead assign in table end **********************/
	/****************** Start Bulk Lead Assign ******************/
	public function assignLeadAction(Request $request)
	{      
		foreach($request->leads as $lead_id)
		{          
			$unq_id = '';
			$old_staus ='';
			$lead_data = LeadsModel::where('lead_id',$lead_id)
					->where('lead_status',0)
					//->where('lead_branch_id',session('application_branch'))
					->first(); 
			
			if(Auth::user()->previlage != 2)
        	{ 
				 $lead_data->where('lead_branch_id',session('application_branch'));
			}
			
			if($lead_data)
			{
				$unq_id = $lead_data->lead_unq_id;
				$old_staus = $lead_data->lead_assigned_status;
			}
			
			$users = DB::table("users")
					->select('name','lname')
					->where('id',$request->user_id)
					->first();
					
			$fdata = FollowupModel::where('followup_lead_id',$lead_data->lead_id)
					->where('followup_status',0)
					->where('followup_branch_id',session('application_branch'))
					->latest()->first();
					
			$followup_current_status = '';
			if($fdata)
			{
				$followup_current_status = $fdata->followup_current_status;	
			}
			
			if($request->date == null)	
			{
				$next_followup_date = date('Y-m-d');
			}
			else
			{
				$next_followup_date = $request->date;
			}
			
			if($followup_current_status == '' || $followup_current_status == null)  // if status null, Assign Lead
			{
				$data = ['followup_ip'     => $request->ip(),
						'followup_on'      => date('Y-m-d H:i:s'),
						'followup_status'  =>0,
						'followup_created' => Auth::user()->id,
						'followup_date'    => date('Y-m-d'),
						'followup_date_on' => date('Y-m-d'),
						'followup_time_on' => date('H:i:s'),	 
						'followup_branch_id' => auth::user()->user_branch,
						'followup_lead_id'   => $lead_id,
						'followup_remarks'   => "Assigned to Staff ".$users->name." ".$users->lname,
						'next_followup_date' => $next_followup_date,
						'followup_type_id'   => 1, // Assign	  
						'followup_current_status'   => 'Assign',
						'followup_assigned_users_id'=> $request->user_id,
						'followup_reg_id'    => $lead_data->lead_reg_id,
						'followup_branch_id' => session('application_branch'),
						//'joining_date'=>$joining_date,
						];
				$res=FollowupModel::create($data);              
			}
			else  // Already Assign Lead Reassigned
			{
				$data = ['followup_ip'     => $request->ip(),
						'followup_on'      => date('Y-m-d H:i:s'),
						'followup_status'  => 0,
						'followup_created' => Auth::user()->id,
						'followup_date'    => date('Y-m-d'),
						'followup_date_on' => date('Y-m-d'),
						'followup_time_on' => date('H:i:s'),	 
						'followup_branch_id' => auth::user()->user_branch,
						'followup_lead_id'   => $lead_id,
						'followup_remarks'   => "Ressigned to Staff ".$users->name." ".$users->lname,
						'next_followup_date' => $next_followup_date,
						'followup_type_id'   => 2, // Reassign
						'followup_current_status'   => 'Reassign',
						'followup_assigned_users_id'=>$request->user_id,
						'followup_reg_id'    => $lead_data->lead_reg_id,
						'followup_branch_id' => session('application_branch'),
						//'joining_date'=>$joining_date,
					];
				$res=FollowupModel::create($data); 
			}
		
			LeadsModel::where('lead_id',$lead_data->lead_id)
				->update(['lead_assigned_users'=>$request->user_id]);   // Update Lead_id & lead assign user_id in lead tatble
        
			if($old_staus == 'New')  // if status New, Assign
			{
				LeadsModel::where('lead_id',$lead_data->lead_id)
						->update(['lead_assigned_status' => 'Assign',
								  'lead_followup_type'   => '1',
							      'lead_followupcreated' => Auth::user()->id]);
			}
			else  // Assign status change to reassign
			{
				LeadsModel::where('lead_id',$lead_data->lead_id)
						->update(['lead_assigned_status' => 'Reassign',
						          'lead_followup_type'   => '2',
						          'lead_followupcreated' => Auth::user()->id]);
			}
		 
			$ip = $request->ip();
            $action = '';
			$user_name = Auth::user()->name;
			$user_id = Auth::user()->id;
			$category = "New Followup";
			$activity = 'New Followup for lead # '.strip_tags($unq_id).' Has been Added By '.$user_name.' ';
			$log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
			Core::userActivityAction($log_array);
		}
      	
		if($old_staus == 'New')
		{
			return response()->json(['heading'=>'Success','text'=>'Leads Assigned Successfully','icon'=>'success']);
		}
		else
		{
			return response()->json(['heading'=>'Success','text'=>'Leads Ressigned Successfully','icon'=>'success']);
		}
	}
	/****************** End Bulk Lead Assign ******************/
	
	/****************** Delete multiple leads ******************/
	public function assignLeadActiondelete(Request $request)
	{
		foreach($request->leads as $lead_id)
		{
			$unq_id ='';
			$lead_data = LeadsModel::where('lead_id',$lead_id);
			if(Auth::user()->previlage != 2)
        	{	
				$lead_data = $lead_data->where('lead_branch_id',session('application_branch'));
			}
				$lead_data = $lead_data->first();
			
			if($lead_data)
			{
				$unq_id= $lead_data->lead_unq_id;
			}

            LeadsModel::where('lead_reg_id',$lead_data->lead_reg_id)->update(['lead_status'=>1]);
            FollowupModel::where('followup_reg_id',$lead_data->lead_reg_id)->update(['followup_status'=>1]);
                      
			$ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
			$activity = 'New Followup for lead # '.strip_tags($unq_id).' Has been Added By '.$user_name.' ';
			$log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity];
			Core::userActivityAction($log_array);  
		}
		
      return response()->json(['heading'=>'Success','text'=>'Leads Deleted Successfully','icon'=>'success']);
	}
	
	public function filterAction(Request $request)
    {
        if($request->from_date)
        {
            session(['filter_lead_fdate'=>$request->from_date]);
        }
        else
        {
            session()->forget('filter_lead_fdate');
        }
        if($request->to_date)
        {
            session(['filter_lead_ldate'=>$request->to_date]);
        }
        else
        {
            session()->forget('filter_lead_ldate');
        }
        if($request->source)
        {
            session(['filter_lead_source'=>$request->source]);
        }
        else
        {
            session()->forget('filter_lead_source');
        }
       
        if($request->type)
        {
			if(session('filter_lead_status')!=$request->type)
			{
				//	session()->forget('filter_staff');
			}
			session(['filter_lead_status'=>$request->type]);
		}
        else
        {
            session()->forget('filter_lead_status');
			session()->forget('filter_staff');
			session()->forget('filter_status');
        }
    }
	
	public function setFilterStaff(Request $request)
	{
		if( $request->staff)
		{
			session(['filter_staff'=>$request->staff]);
		}
		else
		{
			session()->forget('filter_staff');
		}
		return response()->json(['status' => 1]);
	}
	
	public function setFilterStatus(Request $request)
	{
		if( $request->status)
		{
			session(['filter_status'=>$request->status]);
		}
		else
		{
			session()->forget('filter_status');
		}
		return response()->json(['status' => 1]); //return "1";
	}
    
	
	/************ My Leads **************/
	public function view_all_index(Request $request)
    {
		session()->forget('filter_lead_fdate');
		session()->forget('filter_lead_ldate');
        
		if(isset($_GET["id"]))
		{
            $user_id_status = htmlspecialchars($_GET["id"]);
            session(['dashboardstatus'=>$user_id_status]);
		}
		else
		{
			$user_id_status=""; 
            session()->forget('dashboardstatus');
        }
		
        if(isset($_GET["status"]))
		{
			if(htmlspecialchars($_GET["status"])==1)
			{
				// $status=="Assigned";
				session(['assignedstatus'=>'Assigned']);
			}
			else
			{
				session()->forget('assignedstatus');
			}
		}
		else
		{
			session()->forget('assignedstatus');
        }
             
		if(isset($_GET["rejected"]))
		{
			if(htmlspecialchars($_GET["rejected"])==5)
			{
				// $status=="Assigned";
				session(['rejectstatus'=>'Rejected']);
			}
			else
			{
				session()->forget('rejectstatus');
			}
		}
		else
		{
			session()->forget('rejectstatus');
		}
             
		if(isset($_GET["closed"]))
		{
			if(htmlspecialchars($_GET["closed"])==6)
			{
				// $status=="Assigned";
				session(['closedstatus'=>'Closed']);
			}
			else
			{
				session()->forget('closedstatus');
			}
		}
		else
		{
			session()->forget('closedstatus');
        }
             
		if(isset($_GET["reassigned"]))
		{
			if(htmlspecialchars($_GET["reassigned"])==2)
			{
				// $status=="Assigned";
				session(['reassignedstatus'=>'Reassigned']);
			}
			else
			{
                 	session()->forget('reassignedstatus');
			}
		}
		else
		{
			session()->forget('reassignedstatus');
		}
		
			 //////////
			 if(isset($_GET["registered"]))
			 {              
				  if(htmlspecialchars($_GET["registered"])== 4)
				  {
					 // $status=="Assigned";
					  session(['reassignedstatus'=>'Registered']);
				  }         
				 else
				 {
					 session()->forget('registeredstatus');
				 }
             
             }
             else
			 {
                 session()->forget('registeredstatus');
             }
			 ///////////
          
             if(isset($_GET["assigned"]))
			 {
              	if(htmlspecialchars($_GET["assigned"])==1)
				{
                 // $status=="Assigned";
                  session(['assignedstatus'=>'Assigned']);
              	}
            	else
				{
                 	session()->forget('assignedstatus');
             	}
             }
             else
			 {
                 session()->forget('assignedstatus');
             }
          
                  //dd(session('assignedstatus'));
            if(session('filter_campaign'))
			{
				session()->forget('filter_campaign');
			}
		
			if(session('filter_lead_source'))
			{
				session()->forget('filter_lead_source');
			}
			if(session('filter_staff'))
			{
				session()->forget('filter_staff');
			}
		
         $current = \Route::current()->uri();
         $prev = url()->previous();
         
        $sources = DB::table('tbl_source')
                        ->where('source_status',0)
                        ->pluck('source_name','source_id')
                        ->toArray();
        
        $status = DB::table('tbl_followup_type')
				->where('followup_type_status',0)
				->select('followup_type_name','followup_type_id')
				->get();
	 
        $export_option=0;
		$manage_options = DB::table('tbl_menu_set_options')
					   ->select('opset_options')
                       ->where('opset_status', '0')
                       ->where('opset_privilege', '=', Auth::user()->previlage)
                       ->where('opset_main_id', '=',5 )
                       ->where('opset_sub_id', '=', 39)
                       ->orderBy('opset_id')->first();
        if($manage_options)
        {
            $option=$manage_options->opset_options;
            if(strpos($option,'"4"')!==false)
			{
				$export_option=1;
			}
        }
		
		/******** Assign Staff **********/
        $users = DB::table('users')
				->select(DB::raw('concat(name," ",lname) as name,id'))
				->where('status',0);
		$users = $users->where('previlage',49);  // Technicians only
		if(Auth::user()->previlage != 2 && Auth::user()->previlage != 1)
        { 
			//$users = $users->where('user_branch',session('application_branch'));
			$users = $users->where('id',Auth::user()->id);
		} 
		
		$users =  $users->pluck('name','id')->toArray(); 
		
		/******** Assign Staff **********/

        return view('leads::view_index',compact('sources','export_option','users','status'));
    }
	
	
	public function set_lead_session(Request $request)
    {
        session()->forget('followup_reg_id');
        $id = $request->id;
        session(['followup_reg_id'=>$id]);
   
        session(['followup_reg_id_reassign'=>$id]);

        $data = LeadsModel::where('lead_id',$id)
				//->where('lead_reg_id',$id)
                ->leftjoin('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id')               
                ->leftjoin('users','users.id','lead_added_by')
                ->leftjoin('tbl_source','tbl_source.source_id','lead_source')
				->leftjoin('states','states.id','tbl_basic_registration.breg_state')
				->leftjoin('tbl_district','tbl_district.district_id','tbl_basic_registration.breg_district')
				->leftjoin('tbl_branch','tbl_branch.branch_id','tbl_basic_registration.breg_branch_id')
                ->first();
               
		if(Auth::user()->previlage != 2)
        {
			$data->where('lead_branch_id',session('application_branch'));
		}
		
        $dataslead = DB::table('tbl_lead')->where('lead_reg_id')
					->where('tbl_lead.lead_status',0)
					->where('lead_branch_id',session('application_branch'))
					->leftjoin('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id')
					->leftjoin('users','users.id','lead_assigned_users')
					->join('tbl_source','tbl_source.source_id','lead_source')
					->where('tbl_basic_registration.breg_status',0)
					->get();
                 
		if($data)
		{          
			$assigned_staff = DB::table('users')
					->where('id',$data->lead_assigned_users)
					->first();
 
			$follow = DB::table('tbl_lead_followup')
                    ->select('followup_current_status')
                    ->where('followup_lead_id',$data->lead_id)
                    ->where('followup_status',0)
                    ->where('followup_branch_id',session('application_branch'))
                    ->latest()
                    ->first();
					
			/******** Assign Staff **********/
			$users = DB::table('users')
					->select('name', 'id')
					->where('status',0);
			//$users = $users->where('user_branch',session('application_branch'));
			$users = $users->where('previlage',49);  // Technicians only
			if(Auth::user()->previlage != 2 && Auth::user()->previlage != 1)
			{ 
				$users = $users->where('id',Auth::user()->id);
			} 
			$users =  $users->pluck('name','id')->toArray(); //dd($users);
         
			return view('leads::lead_popupview',compact('data','follow','assigned_staff','dataslead','users'));
		}
    }
	
	public function set_lead_session_followtable(Request $request)
    {
		$id = $request->id;
        session()->forget('followupid');
        session(['followupid'=>$id]);

		$data  = FollowupModel::where('followup_status',0)
				->leftjoin('tbl_followup_type','tbl_followup_type.followup_type_id','tbl_lead_followup.followup_current_status','tbl_lead_followup.followup_type_id')
                ->orderBy('followup_id','desc')
                ->where('followup_lead_id',$id)
                //->where('followup_branch_id',session('application_branch'))
                ->get();
		if(Auth::user()->previlage != 2)
        { 
			$data->where('followup_branch_id',session('application_branch'));
		}		
		
        $status1 = DB::table('tbl_followup_type')
                ->where('followup_type_status',0)
                ->pluck('followup_type_name','followup_type_id')
                ->toArray();   
				
        return view('leads::lead_popupview_followup',compact('data','status1'));
    }
	
	public function followup_type_assign(Request $request)
    {
		$assign_status = DB::table('tbl_followup_type')
                ->where('followup_type_status',0)
				->where('followup_type_id','!=', 2)
				->where('followup_type_id','!=', 3)
				->where('followup_type_id','!=', 7)
                ->pluck('followup_type_name','followup_type_id')
                ->toArray();
		 	
		return json_encode($assign_status);
	}
	
	public function followup_type_reassign(Request $request)
    {
		$reassign_status = DB::table('tbl_followup_type')
                ->where('followup_type_status',0)
				->where('followup_type_id','!=', 1)
				->where('followup_type_id','!=', 7)
                ->pluck('followup_type_name','followup_type_id')
                ->toArray();
			
		return json_encode($reassign_status);
	}
	
	
	////////// export ////////////
	public function export()
	{
		ini_set('memory_limit', '-1');
			
		$current_route = url()->previous();
      
		$current_route = explode('/', $current_route);
		$current_route = $current_route[3].'/get-list';

        $today = date('Y-m-d');
        
        $data  = LeadsModel::where('lead_status',0)
                ->join('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id')               
                ->leftjoin('tbl_source','tbl_source.source_id','lead_source')
                ->leftjoin('users','users.id','lead_assigned_users')
				->leftjoin('tbl_branch','tbl_branch.branch_id','breg_branch_id') 
				->leftjoin('tbl_lead_package','tbl_lead_package.lead_pack_lead_id','lead_id')
                ->orderBy('lead_id','desc');
		//$data  = $data->where('lead_branch_id',session('application_branch'));  
		/**	if(Auth::user()->previlage != 2)
			{
				$data->where('lead_branch_id',session('application_branch'));      
			}  **/
		
        if(session('closedstatus'))
		{           
			$data->where('lead_assigned_status',session('closedstatus'));          
        }
        
        if(session('assignedstatus'))
		{           
			$data->where('lead_assigned_status',session('assignedstatus'));          
        } 
       
        // if(Auth::user()->previlage!=2 && $current_route !="leads/get-list" )
        // {             
        //     $data->where('lead_assigned_users',Auth::user()->id);            
        // }
         
        if($current_route=="leads/get-list")
        {
           $manage_options = DB::table('tbl_menu_set_options')
                       ->where('opset_status', '0')
                       ->where('opset_privilege', '=', Auth::user()->previlage)
                       ->where('opset_main_id', '=',5 )
                       ->where('opset_sub_id', '=', 40)
                       ->orderBy('opset_id')->first(); 
					   
            $data->where('lead_date','=',date('Y-m-d'));
        }
        else if($current_route=="myleads/get-list")
        {    
			$manage_options = DB::table('tbl_menu_set_options')
                       ->where('opset_status', '0')
                       ->where('opset_privilege', '=', Auth::user()->previlage)
                       ->where('opset_main_id', '=',5)
					   ->where('opset_sub_id', '=',39)
                       ->orderBy('opset_id')->first();
			
            if(session('assignedstatus'))
			{           
				$data->where('lead_assigned_status',session('assignedstatus'));         
			}
			
			if(session('rejectstatus'))
			{          
				$data->where('lead_assigned_status',session('rejectstatus'));          
			}
        
			if(session('closedstatus'))
			{			   
				$data->where('lead_assigned_status',session('closedstatus'));			  
			}
        
			if(session('reassignedstatus'))
			{
				$data->where('lead_assigned_status',session('reassignedstatus'));
			}
        
              //$data->where('lead_assigned_users',Auth::user()->id);
         }
    
         else if($current_route=="closed/get-list")
         {
              if(session('dashboardstatus'))
			  {
         		$data->where('lead_assigned_users',session('dashboardstatus'));
        	  }
            
              $statuss = "Closed";
              $data->where('lead_assigned_status',$statuss);
             // $data->where('lead_assigned_users',Auth::user()->id);
         }
         else
         {
			 if(session('filter_lead_fdate') && session('filter_lead_ldate'))
			 {
				 $data->whereBetween('lead_date',[session('filter_lead_fdate'),session('filter_lead_ldate')]);
			 }
			 else if(session('filter_lead_fdate'))
			 {
				 $data->where('lead_date','=',session('filter_lead_fdate'));
			 }
			 else if(session('filter_lead_ldate'))
			 {
				 $data->where('lead_date','<=',session('filter_lead_ldate'));
			 }
         }
         
         if(session('filter_lead_source'))
         {
             $data->where('lead_source',session('filter_lead_source'));
         }
         if(session('filter_staff'))
         {
            //  $data->where('lead_assigned_users',session('filter_staff'));
             $data->where('lead_assigned_users',session('filter_staff'));
            //   $data->orWhere('lead_followupcreated',session('filter_staff'));
               $data->Where('lead_status',0);
         }
         if(session('filter_status'))
         {
            if(session('filter_status')=="New")
			{
                $unstatus = " ";
                $data->where('lead_assigned_status',$unstatus);
            }
            else
			{
             	$data->where('lead_assigned_status',session('filter_status')); 
            }
         }
         
          $data = $data->get();    
         
  		return view('leads::export',compact('data'));
   } 
   
   public function getModel(Request $request)
   {
	    $data = DB::table('tbl_model')
			->where('model_make',$request->make)
			->pluck('model_name','model_id');
			
		return $data;
	}
   
   
}
