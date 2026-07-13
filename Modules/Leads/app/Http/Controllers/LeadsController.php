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

		/******** Inspection Templates (for the Assign panel) **********/
		$inspectionTypes = \App\Models\InspectionType::where('is_active', 1)
				->orderBy('id')
				->pluck('name', 'id')
				->toArray();
		/******** Inspection Templates **********/

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

		// Notes for the lead being edited.
		$notes = collect();
		if($data)
		{
			$notes = DB::table('tbl_lead_notes')
				->leftjoin('users','users.id','tbl_lead_notes.note_added_by')
				->where('note_lead_id',$data->lead_id)
				->where('note_status',0)
				->orderBy('note_id','desc')
				->selectRaw("tbl_lead_notes.*, trim(concat(users.name,' ',coalesce(users.lname,''))) as author")
				->get();
		}

		// Latest inspection for the lead being edited (template / technician /
		// scheduled date) so the edit page can show and update them.
		$leadInspection = null;
		if($data)
		{
			$leadInspection = \App\Models\Inspection::where('lead_id',$data->lead_id)->latest('id')->first();
		}

		if(in_array($main_id,json_decode($permission[0]->alloted_mainmenus)) && in_array($sub_id,json_decode($permission[0]->alloted_submenus)))
    	{
        	return view('leads::index',compact('countries','sources','users','states','branch','status','option','district', 'data', 'inspectionTypes', 'notes', 'leadInspection'));
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
								'breg_whatsapp'=> $request->whatsapp,
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
								'breg_whatsapp' => $request->whatsapp,
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
			// Latest inspection per lead (id, assigned date, status) for the table column.
			->addSelect(DB::raw('(SELECT i.id FROM inspections i WHERE i.lead_id = tbl_lead.lead_id ORDER BY i.id DESC LIMIT 1) as inspection_id'))
			->addSelect(DB::raw('(SELECT i.created_at FROM inspections i WHERE i.lead_id = tbl_lead.lead_id ORDER BY i.id DESC LIMIT 1) as inspection_assigned_at'))
			->addSelect(DB::raw('(SELECT i.scheduled_at FROM inspections i WHERE i.lead_id = tbl_lead.lead_id ORDER BY i.id DESC LIMIT 1) as inspection_scheduled_at'))
			->addSelect(DB::raw('(SELECT i.status FROM inspections i WHERE i.lead_id = tbl_lead.lead_id ORDER BY i.id DESC LIMIT 1) as inspection_status'))
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

			// Apply the From/To date range against either the lead-added date
			// (tbl_lead.lead_date) or the inspection scheduled date
			// (inspections.scheduled_at), based on the "Date type" filter chosen
			// on the page. Defaults to the lead-added date.
			$applyDateFilter = function() use ($data)
			{
				$fdate = session('filter_lead_fdate');
				$ldate = session('filter_lead_ldate');
				if(!$fdate && !$ldate) { return; }

				$dateType = session('filter_date_type') ?: 'added';

				if($dateType == 'scheduled')
				{
					$data->whereExists(function($q) use ($fdate,$ldate)
					{
						$q->select(DB::raw(1))
						  ->from('inspections as i')
						  ->whereColumn('i.lead_id','tbl_lead.lead_id')
						  ->whereNotNull('i.scheduled_at');

						if($fdate && $ldate)      { $q->whereBetween(DB::raw('DATE(i.scheduled_at)'), [$fdate,$ldate]); }
						elseif($fdate)            { $q->whereDate('i.scheduled_at','=',$fdate); }
						else                      { $q->whereDate('i.scheduled_at','<=',$ldate); }
					});
				}
				else // lead added date
				{
					if($fdate && $ldate)      { $data->whereBetween('lead_date',[$fdate,$ldate]); }
					elseif($fdate)            { $data->where('lead_date','=',$fdate); }
					else                      { $data->where('lead_date','<=',$ldate); }
				}
			};

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
			
			else if($current_route=="myleads/get-list" || $current_route=="leadslist/get-list")
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
              
				$applyDateFilter();
			}
			else
			{
				$applyDateFilter();
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
			
			 
		// Assignment filter (All / Assigned / Unassigned) — from the leadslist page.
		if($request->assign_status == 'assigned')
		{
			$data->whereNotNull('tbl_lead.lead_assigned_users')
				 ->where('tbl_lead.lead_assigned_users','!=','')
				 ->where('tbl_lead.lead_assigned_users','!=','0');
		}
		else if($request->assign_status == 'unassigned')
		{
			$data->where(function($q){
				$q->whereNull('tbl_lead.lead_assigned_users')
				  ->orWhere('tbl_lead.lead_assigned_users','')
				  ->orWhere('tbl_lead.lead_assigned_users','0');
			});
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
	
		// Count once — cheaply. Wrap only the grouped lead ids (drops the 3
		// correlated inspection sub-selects and all the heavy columns), so the
		// total is a single lightweight query instead of running the full query
		// twice just to count() it in PHP.
		$total = DB::query()->fromSub((clone $data)->select('tbl_lead.lead_id'), 'sub')->count();

		// Fetch only the current page — the per-row inspection sub-selects now
		// run for $limit rows instead of the entire table.
		$dataMod = $data->skip($offset)->take($limit)->get();

		$datas = [
			"iTotalDisplayRecords" => $total,
			"iTotalRecords"        => $total,
			"TotalDisplayRecords"  => $limit,
			'option'               => $option,
			'data'                 => $dataMod->toArray(),
		];
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
                ->first();   
      
		$flag = DB::table("tbl_country")->selct('country_code')->where('id')->first();
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
            $mobilecodedata  = $request->mobilecodedata;
    	    $country         = explode("+", $mobilecodedata);
			$country    = 91;
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
								'breg_whatsapp'  => $request->whatsapp,
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
							   'breg_whatsapp'=> $request->whatsapp,
							];
				$basic_arr       = RegistrationModel::create($basic_data);
                $registrtaion_id = $basic_arr->id;
                $activity        = 'New Basic Reg ID '.$registrtaion_id.' Has been added By '.Auth::user()->name.' ';
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
			
			$modelArray = $request->model;  
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
					'make_model_year'=> $request->make_model_year,
                    //'lead_assigned_users'=>'',
                    ]  ;
           
            $lid    = $request->lead_id;
            $unq_id = $res->lead_unq_id;
            LeadsModel::where('lead_id',$request->lead_id)->update($lead);
            /******** Update into lead end ********/
			
			/********** Update into PACKAGE START**********/
			$mode_pay    = $request->payment;
			$package_id  = $request->lpackage;  // package ID // lead pachage for Book inspection
			 
			if($package_id != null && $package_id != '' )
			{
				$packname = DB::table('tbl_package')
						->select('package_name','package_id','package_payable')
						->where('package_status',0)
						->where('package_id',$package_id)
						->first();

				$package_name = $packname->package_name ?? ''; //package Name
				$package_pay  = $packname->package_payable ?? ''; //package Name

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

				// Insert the package row if the lead has none yet, otherwise update it.
				// (The old code only UPDATE'd, so leads without a package row could
				// never have their Package / Mode of Payment saved.)
				DB::table('tbl_lead_package')
						->updateOrInsert(['lead_pack_lead_id' => $lid], $packagedata);
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

		$unq_id  = '';
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
		$assigned  = 0;   // leads actually (re)assigned
		$skipped   = 0;   // leads skipped because their inspection is already completed
		$old_staus = '';

		foreach($request->leads as $lead_id)
		{
			// Guard: a lead whose inspection is already completed must never be
			// (re)assigned. Skip it and report the count back to the UI so the
			// user sees why some of their selected leads were not assigned.
			$latestInspStatus = \App\Models\Inspection::where('lead_id', $lead_id)
				->orderByDesc('id')->value('status');
			if ($latestInspStatus === \App\Models\Inspection::STATUS_COMPLETED) {
				$skipped++;
				continue;
			}

			// Per-lead inspection template / date / scheduled date (Multiple Lead
			// Assignment sends these as arrays keyed by lead id). Falls back to a
			// single scalar value when other callers post one value for all leads.
			$typeForLead  = is_array($request->inspection_type_id) ? ($request->inspection_type_id[$lead_id] ?? null) : $request->inspection_type_id;
			$schedForLead = is_array($request->scheduled_at)       ? ($request->scheduled_at[$lead_id] ?? null)       : $request->scheduled_at;
			$dateForLead  = is_array($request->date)               ? ($request->date[$lead_id] ?? null)               : $request->date;
			// Per-lead assignee (individual mode) with single-value fallback (assign-all mode).
			$userForLead  = is_array($request->user_id)            ? ($request->user_id[$lead_id] ?? null)            : $request->user_id;

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
					->where('id',$userForLead)
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
			
			if($dateForLead == null)
			{
				$next_followup_date = date('Y-m-d');
			}
			else
			{
				$next_followup_date = $dateForLead;
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
						'followup_assigned_users_id'=> $userForLead,
						'followup_reg_id'    => $lead_data->lead_reg_id,
						'followup_branch_id' => session('application_branch'),
						//'joining_date'=>$joining_date,
						];
				$res = FollowupModel::create($data);              
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
						'followup_assigned_users_id'=>$userForLead,
						'followup_reg_id'    => $lead_data->lead_reg_id,
						'followup_branch_id' => session('application_branch'),
						//'joining_date'=>$joining_date,
					];
				$res = FollowupModel::create($data); 
			}
		
			LeadsModel::where('lead_id',$lead_data->lead_id)
				->update(['lead_assigned_users'=>$userForLead]);   // Update Lead_id & lead assign user_id in lead tatble

			// Inspection assignment is now part of the normal lead assignment:
			// the assigned staff becomes the inspection technician, using the
			// selected template + (optional) schedule chosen on the Assign panel.
			\App\Models\Inspection::createForLead(
				(int) $lead_data->lead_id,
				(int) $userForLead,
				$schedForLead ?: null,
				$typeForLead ? (int) $typeForLead : null
			);

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

			$assigned++;
		}

		$noun = ($old_staus == 'New') ? 'Assigned' : 'Ressigned';
		if ($assigned > 0)
		{
			$text = $assigned.' lead(s) '.$noun.' Successfully';
			if ($skipped > 0)
			{
				$text .= '. '.$skipped.' completed inspection(s) skipped (cannot be reassigned).';
			}
		}
		else
		{
			$text = 'No leads assigned. '.$skipped.' completed inspection(s) cannot be reassigned.';
		}

		return response()->json([
			'heading'  => 'Success',
			'text'     => $text,
			'icon'     => $assigned > 0 ? 'success' : 'warning',
			'assigned' => $assigned,
			'skipped'  => $skipped,
		]);
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
				$unq_id = $lead_data->lead_unq_id;
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
        // Which date column the From/To range applies to: 'added' (lead added
        // date) or 'scheduled' (inspection scheduled date).
        if($request->date_type)
        {
            session(['filter_date_type'=>$request->date_type]);
        }
        else
        {
            session()->forget('filter_date_type');
        }
        // Staff and Status are set here as well, so a single Apply request writes
        // EVERY filter to the session atomically. Sending them as separate
        // concurrent requests caused a race (partial application) that forced the
        // user to click Apply several times before the results were correct.
        if($request->has('staff'))
        {
            if($request->staff !== null && $request->staff !== '') { session(['filter_staff'=>$request->staff]); }
            else { session()->forget('filter_staff'); }
        }
        if($request->has('status'))
        {
            if($request->status !== null && $request->status !== '') { session(['filter_status'=>$request->status]); }
            else { session()->forget('filter_status'); }
        }
        // Assigned/Unassigned filter — stored in the session so the Excel export
        // (a plain GET link) can apply the same filter the table shows.
        if($request->has('assign_status'))
        {
            if($request->assign_status) { session(['filter_assign_status'=>$request->assign_status]); }
            else { session()->forget('filter_assign_status'); }
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
			// NOTE: do NOT clear filter_staff / filter_status here. This action
			// runs on every From/To date and Source change (which send no `type`),
			// so clearing them was silently wiping the Staff and Status filters
			// whenever the user touched a date or source. The Staff/Status filters
			// are cleared by their own controls and by the page-load reset in
			// view_all_index() (used by the Reset button, which reloads the page).
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
			if(session('filter_status'))
			{
				session()->forget('filter_status');
			}
			if(session('filter_date_type'))
			{
				session()->forget('filter_date_type');
			}
			if(session('filter_assign_status'))
			{
				session()->forget('filter_assign_status');
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
            $option = $manage_options->opset_options;
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
		// Inspection templates for the Multiple Lead Assignment modal.
		$inspectionTypes = \App\Models\InspectionType::where('is_active', 1)
			->orderBy('id')
			->pluck('name', 'id')
			->toArray();

        return view('leads::view_index',compact('sources','export_option','users','status','inspectionTypes'));
    }

	/**
	 * "Leads" — a duplicate of My Leads (view_all_index) exposed under the
	 * /leadslist route. Same page, same details.
	 */
	public function leadsListIndex(Request $request)
    {
        return $this->view_all_index($request);
    }

	/**
	 * Update a lead's inspection template, assigned technician and scheduled
	 * date from the edit-lead page (AJAX). Creates the inspection if the lead
	 * doesn't have one yet (only when a technician is chosen).
	 */
	public function updateLeadInspection(Request $request)
	{
		$request->validate([
			'lead_id'            => ['required', 'integer'],
			'inspection_type_id' => ['nullable', 'integer'],
			'technician_id'      => ['nullable', 'integer'],
			'scheduled_at'       => ['nullable', 'date'],
		]);

		$leadId = (int) $request->lead_id;
		$tech   = (int) $request->technician_id;
		$typeId = $request->inspection_type_id ? (int) $request->inspection_type_id : null;
		$sched  = $request->scheduled_at ?: null;

		$inspection = \App\Models\Inspection::where('lead_id', $leadId)->latest('id')->first();

		if($tech > 0)
		{
			// Create or re-point the inspection to the chosen technician/template/date.
			$inspection = \App\Models\Inspection::createForLead($leadId, $tech, $sched, $typeId);

			// Keep the lead's assigned user in sync so the list's "Assign To"
			// reflects the assigned person.
			LeadsModel::where('lead_id', $leadId)->update(['lead_assigned_users' => $tech]);
		}
		elseif($inspection)
		{
			// No technician chosen — just update the template/date on the existing inspection.
			if($typeId) { $inspection->inspection_type_id = $typeId; }
			$inspection->scheduled_at = $sched;
			$inspection->save();
		}
		else
		{
			return response()->json(['status' => 0, 'text' => 'Select an assigned person to create the inspection.']);
		}

		$ip = $request->ip();
		$activity = 'Inspection details updated for lead # '.$leadId.' by '.Auth::user()->name;
		Core::userActivityAction(['activity_ip'=>$ip, 'activity_action'=>'', 'activity_user'=>Auth::user()->name, 'activity_user_id'=>Auth::user()->id, 'activity_desc'=>$activity, 'activity_category'=>'Update Inspection']);

		return response()->json(['status' => 1, 'text' => 'Inspection details updated']);
	}

	/**
	 * Add a note to a lead (AJAX).
	 */
	public function addNote(Request $request)
    {
        $request->validate([
            'note_lead_id' => ['required', 'integer'],
            'note_text'    => ['required', 'string', 'max:5000'],
        ]);

        $id = DB::table('tbl_lead_notes')->insertGetId([
            'note_lead_id'  => (int) $request->note_lead_id,
            'note_text'     => trim($request->note_text),
            'note_added_by' => Auth::id(),
            'note_status'   => 0,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $note = DB::table('tbl_lead_notes')
            ->leftjoin('users', 'users.id', 'tbl_lead_notes.note_added_by')
            ->where('note_id', $id)
            ->selectRaw("tbl_lead_notes.*, trim(concat(users.name,' ',coalesce(users.lname,''))) as author")
            ->first();

        return response()->json(['status' => 1, 'note' => $note]);
    }

	/**
	 * Update the text of a lead note (AJAX).
	 */
	public function updateNote(Request $request)
    {
        $request->validate([
            'note_id'   => ['required', 'integer'],
            'note_text' => ['required', 'string', 'max:5000'],
        ]);

        DB::table('tbl_lead_notes')
            ->where('note_id', (int) $request->note_id)
            ->update(['note_text' => trim($request->note_text), 'updated_at' => now()]);

        return response()->json(['status' => 1, 'note_text' => trim($request->note_text)]);
    }

	/**
	 * Soft-delete a lead note (AJAX).
	 */
	public function deleteNote(Request $request)
    {
        DB::table('tbl_lead_notes')
            ->where('note_id', (int) $request->note_id)
            ->update(['note_status' => 1, 'updated_at' => now()]);

        return response()->json(['status' => 1]);
    }

	/**
	 * Read-only detail page for a single lead.
	 */
	public function viewLead($id)
    {
        $data = LeadsModel::where('lead_id', $id)
            ->leftjoin('tbl_basic_registration', 'tbl_basic_registration.breg_id', 'tbl_lead.lead_reg_id')
            ->leftjoin('users', 'users.id', 'lead_added_by')
            ->leftjoin('tbl_source', 'tbl_source.source_id', 'lead_source')
            ->leftjoin('states', 'states.id', 'tbl_basic_registration.breg_state')
            ->leftjoin('tbl_district', 'tbl_district.district_id', 'tbl_basic_registration.breg_district')
            ->leftjoin('tbl_branch', 'tbl_branch.branch_id', 'tbl_basic_registration.breg_branch_id')
            ->select(
                // Lead
                'tbl_lead.lead_id', 'tbl_lead.lead_unq_id', 'tbl_lead.lead_date', 'tbl_lead.lead_date_on', 'tbl_lead.lead_time_on',
                'tbl_lead.lead_datetime', 'tbl_lead.lead_source_name', 'tbl_lead.lead_make', 'tbl_lead.lead_model',
                'tbl_lead.lead_year', 'tbl_lead.make_model_year', 'tbl_lead.lead_year_from', 'tbl_lead.lead_year_to',
                'tbl_lead.lead_vehicle_plate_no', 'tbl_lead.lead_color', 'tbl_lead.lead_color_ar',
                'tbl_lead.lead_seller_name', 'tbl_lead.lead_seller_name_ar', 'tbl_lead.lead_seller_mobile', 'tbl_lead.lead_your_mobile',
                'tbl_lead.lead_location', 'tbl_lead.lead_budget', 'tbl_lead.lead_know_more', 'tbl_lead.lead_add_details',
                'tbl_lead.lead_form_type', 'tbl_lead.lead_tracking_id', 'tbl_lead.lead_assigned_status', 'tbl_lead.lead_followup_type',
                'tbl_lead.lead_enq_status', 'tbl_lead.lead_remarks', 'tbl_lead.lead_assigned_users', 'tbl_lead.lead_reg_id',
                // Registration
                'tbl_basic_registration.breg_fname', 'tbl_basic_registration.breg_fname_ar', 'tbl_basic_registration.breg_mob',
                'tbl_basic_registration.breg_mob_code', 'tbl_basic_registration.breg_email', 'tbl_basic_registration.breg_whatsapp',
                'tbl_basic_registration.breg_place', 'tbl_basic_registration.breg_message', 'tbl_basic_registration.breg_qualification',
                'tbl_basic_registration.breg_date',
                // Joined names
                'users.name as added_by_name', 'tbl_source.source_name', 'states.name as state_name',
                'tbl_district.district_name', 'tbl_branch.branch_name'
            )
            ->first();

        if (! $data) {
            abort(404, 'Lead not found');
        }

        // Resolve make/model ids to names for display. lead_make is a single id;
        // lead_model may hold comma-separated ids. Non-numeric legacy values are
        // left as-is.
        if (is_numeric($data->lead_make)) {
            $data->lead_make = DB::table('tbl_make')->where('make_id', $data->lead_make)->value('make_name') ?: $data->lead_make;
        }
        if ($data->lead_model !== null && $data->lead_model !== '') {
            $modelIds = array_filter(array_map('trim', explode(',', $data->lead_model)));
            $modelNames = DB::table('tbl_model')->whereIn('model_id', $modelIds)->pluck('model_name')->toArray();
            if ($modelNames) {
                $data->lead_model = implode(', ', $modelNames);
            }
        }

        $assigned_staff = $data->lead_assigned_users
            ? DB::table('users')->where('id', $data->lead_assigned_users)->first()
            : null;

        $followups = DB::table('tbl_lead_followup')
            ->leftjoin('users', 'users.id', 'tbl_lead_followup.followup_assigned_users_id')
            ->where('followup_lead_id', $id)
            ->where('followup_status', 0)
            ->orderBy('followup_id', 'desc')
            ->select('tbl_lead_followup.*', 'users.name as staff_name')
            ->get();

        $inspection = DB::table('inspections')->where('lead_id', $id)->orderBy('id', 'desc')->first();

        // Technicians for the in-page assignment option.
        $technicians = DB::table('users')
            ->selectRaw("id, trim(concat(name, ' ', coalesce(lname, ''))) as name")
            ->where('status', 0)
            ->where('previlage', 49)
            ->orderBy('name')
            ->get();

        // Inspection templates (same source the edit page uses).
        $inspectionTypes = \App\Models\InspectionType::where('is_active', 1)
            ->orderBy('id')
            ->pluck('name', 'id')
            ->toArray();

        // Notes for this lead.
        $notes = DB::table('tbl_lead_notes')
            ->leftjoin('users', 'users.id', 'tbl_lead_notes.note_added_by')
            ->where('note_lead_id', $id)
            ->where('note_status', 0)
            ->orderBy('note_id', 'desc')
            ->selectRaw("tbl_lead_notes.*, trim(concat(users.name,' ',coalesce(users.lname,''))) as author")
            ->get();

        return view('leads::lead_view', compact('data', 'assigned_staff', 'followups', 'inspection', 'technicians', 'inspectionTypes', 'notes'));
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

			// Latest inspection for this lead (for the "Open inspection" link on the followup page).
			$inspection = \App\Models\Inspection::where('lead_id',$data->lead_id)->latest('id')->first();

			return view('leads::lead_popupview',compact('data','follow','assigned_staff','dataslead','users','inspection'));
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
			
		// Build the SAME filtered result set the leadslist/myleads table shows,
		// reading the identical session filters. The old code detected the page
		// from url()->previous() and, when that resolved to "leads/get-list",
		// forced a "today only" filter (hence the tiny row count); it also never
		// applied the date-range filter on the leadslist branch, so filtered
		// exports did not match the on-screen list.
		$privilege = Auth::user()->previlage;

		$data = LeadsModel::where('tbl_lead.lead_status', 0)
				->leftjoin('tbl_basic_registration', 'tbl_basic_registration.breg_id', 'tbl_lead.lead_reg_id')
				->leftjoin('tbl_source', 'tbl_source.source_id', 'tbl_lead.lead_source')
				->select(
					'tbl_lead.lead_id', 'tbl_lead.lead_reg_id', 'tbl_lead.lead_date',
					'tbl_lead.lead_source', 'tbl_lead.lead_assigned_users',
					'tbl_basic_registration.breg_fname', 'tbl_basic_registration.breg_email', 'tbl_basic_registration.breg_mob',
					'tbl_source.source_name'
				)
				->orderBy('tbl_lead.lead_id', 'desc');

		// Non-admins see only the leads they added (same as the table).
		if($privilege != 1 && $privilege != 2)
		{
			$data->where('tbl_lead.lead_added_by', Auth::user()->id);
		}

		// Date range - against the lead added date or the inspection scheduled date.
		$fdate = session('filter_lead_fdate');
		$ldate = session('filter_lead_ldate');
		if($fdate || $ldate)
		{
			$dateType = session('filter_date_type') ?: 'added';
			if($dateType == 'scheduled')
			{
				$data->whereExists(function($q) use ($fdate, $ldate)
				{
					$q->select(DB::raw(1))->from('inspections as i')
					  ->whereColumn('i.lead_id', 'tbl_lead.lead_id')
					  ->whereNotNull('i.scheduled_at');
					if($fdate && $ldate)  { $q->whereBetween(DB::raw('DATE(i.scheduled_at)'), [$fdate, $ldate]); }
					elseif($fdate)        { $q->whereDate('i.scheduled_at', '=', $fdate); }
					else                  { $q->whereDate('i.scheduled_at', '<=', $ldate); }
				});
			}
			else
			{
				if($fdate && $ldate)  { $data->whereBetween('tbl_lead.lead_date', [$fdate, $ldate]); }
				elseif($fdate)        { $data->where('tbl_lead.lead_date', '=', $fdate); }
				else                  { $data->where('tbl_lead.lead_date', '<=', $ldate); }
			}
		}

		if(session('filter_lead_source'))
		{
			$data->where('tbl_lead.lead_source', session('filter_lead_source'));
		}
		if(session('filter_staff'))
		{
			$data->where('tbl_lead.lead_assigned_users', session('filter_staff'));
		}
		if(session('filter_status'))
		{
			if(session('filter_status') == "New") { $data->where('tbl_lead.lead_assigned_status', ' '); }
			else { $data->where('tbl_lead.lead_assigned_status', session('filter_status')); }
		}

		// Assigned / Unassigned (same as the table).
		$assign = session('filter_assign_status');
		if($assign == 'assigned')
		{
			$data->whereNotNull('tbl_lead.lead_assigned_users')
				 ->where('tbl_lead.lead_assigned_users', '!=', '')
				 ->where('tbl_lead.lead_assigned_users', '!=', '0');
		}
		else if($assign == 'unassigned')
		{
			$data->where(function($q){
				$q->whereNull('tbl_lead.lead_assigned_users')
				  ->orWhere('tbl_lead.lead_assigned_users', '')
				  ->orWhere('tbl_lead.lead_assigned_users', '0');
			});
		}

		$data = $data->get();

		// Export as a real CSV. HTML-as-.xls rendered inconsistently across
		// spreadsheet apps (raw markup / broken columns); a CSV opens as a clean
		// grid everywhere. Streamed so large lists don't exhaust memory.
		$filename = 'leads_'.date('Y-m-d_His').'.csv';

		$headings = ['#','First Name','Email','Mobile','Package','Source','Staff',
					 'Current Status','Last Comment','Lead Date','Assign Date','Last Status Update Date'];

		$callback = function() use ($data, $headings)
		{
			$out = fopen('php://output', 'w');
			// UTF-8 BOM so Excel shows accented / Arabic text correctly.
			fwrite($out, "\xEF\xBB\xBF");
			fputcsv($out, $headings);

			$i = 0;
			foreach($data as $row)
			{
				$i++;

				// Latest followup for this lead (current status / last comment /
				// last update date), and the latest Assign/Reassign followup
				// (assign date) — same lookups the old export view used.
				$latest = DB::table('tbl_lead_followup')
						->where('followup_reg_id',$row->lead_reg_id)
						->where('followup_status',0)
						->latest()->first();

				$latestAssign = DB::table('tbl_lead_followup')
						->where('followup_reg_id',$row->lead_reg_id)
						->where('followup_status',0)
						->where(function($q){ $q->where('followup_current_status','Reassign')->orWhere('followup_current_status','Assign'); })
						->latest()->first();

				// Staff: assigned user, else the assign/reassign followup's user,
				// else the creator of the latest followup.
				$staff = '';
				$user  = DB::table('users')->where('id',$row->lead_assigned_users)->first();
				if($user)
				{
					$staff = trim($user->name.' '.$user->lname);
				}
				elseif($latestAssign && $latestAssign->followup_assigned_users_id)
				{
					$u = DB::table('users')->where('id',$latestAssign->followup_assigned_users_id)->first();
					if($u) { $staff = trim($u->name.' '.$u->lname); }
				}
				elseif(!$latestAssign && $latest && $latest->followup_created)
				{
					$u = DB::table('users')->where('id',$latest->followup_created)->first();
					if($u) { $staff = trim($u->name.' '.$u->lname); }
				}

				$currentStatus = $latest ? $latest->followup_current_status : '';
				$lastComment   = $latest ? $latest->followup_remarks : '';
				$leadDate      = $row->lead_date ? date('d/m/Y', strtotime($row->lead_date)) : '';
				$assignDate    = $latestAssign ? date('d-m-Y', strtotime($latestAssign->followup_date)) : '';
				$lastUpdate    = $latest ? date('d/m/Y', strtotime($latest->followup_date)) : '';

				// Package name (fetched per-row so the base query needs no join
				// that could multiply/duplicate lead rows).
				$package = DB::table('tbl_lead_package')
						->where('lead_pack_lead_id', $row->lead_id)
						->orderBy('lead_pack_id', 'desc')
						->value('lead_pack_name');

				fputcsv($out, [
					$i,
					$row->breg_fname,
					$row->breg_email,
					$row->breg_mob,
					$package,
					$row->source_name,
					$staff,
					$currentStatus,
					$lastComment,
					$leadDate,
					$assignDate,
					$lastUpdate,
				]);
			}

			fclose($out);
		};

		return response()->streamDownload($callback, $filename, [
			'Content-Type' => 'text/csv; charset=UTF-8',
		]);
   }
   
   public function getModel(Request $request)
   {
	    $data = DB::table('tbl_model')
			->where('model_make',$request->make)
			->pluck('model_name','model_id');

		return $data;
	}
}
