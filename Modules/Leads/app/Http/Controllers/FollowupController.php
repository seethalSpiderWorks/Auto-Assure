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
use Mail;
use Str;

class FollowupController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $current_route = \Route::current()->uri();
        $prev_route = url()->previous();
        if($current_route!=$prev_route)
        {
            session()->forget('filter_lead_source');
            session()->forget('filter_lead_fdate');
            session()->forget('filter_lead_ldate');
            session()->forget('filter_lead_status');
    		session()->forget('filter_staff');
    		session()->forget('filter_status');
    		session()->forget('filter_campaign');
        }
         
        $sources = DB::table('tbl_source')
                ->where('source_status',0)
                ->pluck('source_name','source_id')
                ->toArray();
		 
        $export_option = 0;
        $manage_options= DB::table('tbl_menu_set_options')
                       ->where('opset_status', '0')
                       ->where('opset_privilege', '=', Auth::user()->previlage)
                       ->where('opset_main_id', '=',5 )
                       ->where('opset_sub_id', '=', 41)
                       ->orderBy('opset_id')->first();
        if($manage_options)
        {
            $option=$manage_options->opset_options;
            if(strpos($option,'"4"')!==false)
		    {
		        $export_option=1;
		    }
        }
		 
        $users = DB::table('users')
			->select(DB::raw('concat(name," ",lname) as name,id'))
			->where('status',0);
		$users = $users->where('previlage',49);  // Technicians only
		if(Auth::user()->previlage != 2 && Auth::user()->previlage != 1)
		{ 
			$users = $users->where('id',Auth::user()->id);
		} 
		$users =  $users->pluck('name','id')->toArray();
			
		/* ->pluck('name','id')
			->toArray(); */
		 
		/**	if(Auth::user()->previlage != 2)
			{ **/
				//$users = $users->where('user_branch',session('application_branch'));
		/**	} **/
    	
		$data  = DB::table('tbl_lead')
                ->where('followup_status',0)
                ->where('lead_status',0)
                //->where('lead_branch_id',session('application_branch'))
                ->whereNotNull('next_followup_date')
			    //->whereRaw('Date(next_followup_date) = CURDATE()')
                ->leftJoin('tbl_lead_followup', function($query) 
                    {
                        $query->on('tbl_lead_followup.followup_reg_id','=','lead_reg_id')
                        ->whereRaw('tbl_lead_followup.followup_id IN (select MAX(a2.followup_id) from tbl_lead_followup as a2 join tbl_lead as u2 on u2.lead_reg_id = a2.followup_reg_id group by u2.lead_reg_id)');
                    })    
                ->join('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id')
                ->leftjoin('users','users.id','followup_created')
                ->leftjoin('tbl_source','tbl_source.source_id','lead_source')
				->leftjoin('tbl_track_variables','tbl_track_variables.track_id','lead_tracking_id')
                //->orderBy('next_followup_date','asc')
                ->groupBy('lead_reg_id')
			    ->orderBy('tbl_lead_followup.next_followup_date', 'ASC')->get();
		 
		  /** if(Auth::user()->previlage != 2)
			{**/
				 $data->where('lead_branch_id',session('application_branch')); 
		 /** }**/
		 
		// session()->put('key',$data->next_followup_date);
		// dd(session()->get('key'));

        return view('leads::followups',compact('sources','export_option','users','data'));
    }
     
    public function datatable(Request $request)
    {
        $current_route = \Route::current()->uri();
		
		$limit   = ($request->length != '') ? $request->length : 10;
		$offset  = ($request->start != '') ? $request->start : 0;
		$search  = $request->search['value'];
		$order   = $request->order;
        $columns = $request->columns;
        $colName = 'tbl_lead.lead_id';
        $sort    = 'desc';
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
         
        $data  = DB::table('tbl_lead')
                ->where('followup_status',0)
                ->where('lead_status',0)
                //->where('lead_branch_id',session('application_branch'))
                ->whereNotNull('next_followup_date')
			    //->whereRaw('Date(next_followup_date) = CURDATE()')
                ->leftJoin('tbl_lead_followup', function($query) 
                    {
                        //  $query->on('tbl_lead_followup.followup_reg_id','=','lead_reg_id')
					    $query->on('tbl_lead_followup.followup_lead_id','=','lead_id')
                        ->whereRaw('tbl_lead_followup.followup_id IN (select MAX(a2.followup_id) from tbl_lead_followup as a2 join tbl_lead as u2 on u2.lead_id = a2.followup_lead_id group by u2.lead_id)');
                    })    
                ->join('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id')
                ->leftjoin('users','users.id','followup_assigned_users_id')
                ->leftjoin('tbl_source','tbl_source.source_id','lead_source')
                //->orderBy('next_followup_date','asc')
        //11/06/25->groupBy('lead_reg_id')
				->Where(function($query) use ($search) 
                {
					$query->where('users.name', 'like', $search . '%');
					$query->orWhere('tbl_lead_followup.followup_current_status', 'like', $search . '%');
					$query->orWhere('tbl_basic_registration.breg_fname', 'like', $search . '%');
					$query->orWhere('tbl_basic_registration.breg_mob', 'like', $search . '%');
					$query->orWhere('tbl_source.source_name', 'like', $search . '%');
					$query->orWhere('tbl_lead.lead_unq_id', 'like', $search . '%');
                });
       
		if($privilege != 1 && $privilege != 2)
        { 
			//$data->where('lead_branch_id',session('application_branch')); 
			$data->where('lead_assigned_users',Auth::user()->id); 
		}  
		
		/*if(Auth::user()->previlage!=2)
         {
            $data->where('followup_created',Auth::user()->id);
         } */
		 
        $data->where('next_followup_date','=',date('Y-m-d')); 
        if(session('filter_lead_fdate') && session('filter_lead_ldate'))
        {
            $data->whereBetween('next_followup_date',[session('filter_lead_fdate'),session('filter_lead_ldate')]);
        }
        else if(session('filter_lead_fdate'))
        {
            /** $data->where('lead_date','>=',session('filter_lead_fdate')); **/
			$data->where('next_followup_date','=',session('filter_lead_fdate'));
        }
        else if(session('filter_lead_ldate'))
        {
            /** $data->where('lead_date','<=',session('filter_lead_ldate')); **/
			$data->where('next_followup_date','<=',session('filter_lead_ldate'));
        }
        
        if(session('filter_lead_source'))
        {
            $data->where('lead_source',session('filter_lead_source'));
        }
		
		if(session('filter_campaign'))
		{
			$data->where('lead_tracking_id',session('filter_campaign'));
		} 
	
		if(session('filter_staff'))
		{       
			   $data->where('lead_assigned_users',session('filter_staff'));
		}
		
		// if(session()->get('key'))
		//{
			   /////$data->whereDate('next_followup_date', '=', date('Y-m-d') );
		// }
		
		/* if(session('year') && session('month'))
		{
			   $data->whereDate('next_followup_date', '=', session()->get('key'));
		 } */
         
        //$data=$data->get();
		
		if ($colName != '' && $sort != '') 
		{
            $data->orderBy($colName, $sort);
        } 
		else 
		{
           $data->orderBy('tbl_lead_followup.next_followup_date', 'ASC');
        }
		
		$datas = ["iTotalDisplayRecords" => $data->count(), "iTotalRecords" => $data->count(), "TotalDisplayRecords" => $limit];
		$dataMod = $data->skip($offset)->take($limit)->get();
		$datas['data'] = $dataMod->toArray();
		return response()->json($datas);
    }
    
	function gettoday(Request $request)
    {
		session(['today'=>$request->date]);
	}
	
	/****************************************/
	public function datatable_all(Request $request)
    {
        $current_route = \Route::current()->uri();
		
		$limit   = ($request->length != '') ? $request->length : 10;
		$offset  = ($request->start != '') ? $request->start : 0;
		$search  = $request->search['value'];
		$order   = $request->order;
        $columns = $request->columns;
        $colName = 'tbl_lead.lead_id';
        $sort    = 'desc';
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
         
        $data  = DB::table('tbl_lead')
                ->where('followup_status',0)
                ->where('lead_status',0)
                //->where('lead_branch_id',session('application_branch'))
                ->whereNotNull('next_followup_date')
                ->leftJoin('tbl_lead_followup', function($query) 
                    {
                        //$query->on('tbl_lead_followup.followup_reg_id','=','lead_reg_id')
					    $query->on('tbl_lead_followup.followup_lead_id','=','lead_id')
                        ->whereRaw('tbl_lead_followup.followup_id IN (select MAX(a2.followup_id) from tbl_lead_followup as a2 join tbl_lead as u2 on u2.lead_id = a2.followup_lead_id group by u2.lead_id)');
                    })    
                ->join('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id')
                ->leftjoin('users','users.id','followup_assigned_users_id')
                ->leftjoin('tbl_source','tbl_source.source_id','lead_source')
				->leftjoin('tbl_track_variables','tbl_track_variables.track_id','lead_tracking_id')
                //->orderBy('next_followup_date','asc')
                //->groupBy('lead_reg_id')
				->Where(function($query) use ($search) 
                {
					$query->where('users.name', 'like', $search . '%');
					$query->orWhere('tbl_lead_followup.followup_current_status', 'like', $search . '%');
					$query->orWhere('tbl_basic_registration.breg_fname', 'like', $search . '%');
					$query->orWhere('tbl_basic_registration.breg_mob', 'like', $search . '%');
					$query->orWhere('tbl_source.source_name', 'like', $search . '%');
					$query->orWhere('tbl_track_variables.track_variable', 'like', $search . '%');
					$query->orWhere('tbl_lead.lead_unq_id', 'like', $search . '%');
                });
		
		if($privilege != 1 && $privilege != 2)
        { 
			 //$data->where('lead_branch_id',session('application_branch')); 
			 $data->where('lead_assigned_users',Auth::user()->id); 
		} 
 		 
		/*if(Auth::user()->previlage!=2)
         {
            $data->where('followup_created',Auth::user()->id);
         }*/
         
        if(session('filter_lead_fdate') && session('filter_lead_ldate'))
        {
            $data->whereBetween('next_followup_date',[session('filter_lead_fdate'),session('filter_lead_ldate')]);
        }
        else if(session('filter_lead_fdate'))
        {
			$data->where('next_followup_date','=',session('filter_lead_fdate'));
        }
        else if(session('filter_lead_ldate'))
        {
			$data->where('next_followup_date','<=',session('filter_lead_ldate'));
        }
        
        if(session('filter_lead_source'))
        {
            $data->where('lead_source',session('filter_lead_source'));
        }
		
		if(session('filter_campaign'))
		{
			$data->where('lead_tracking_id',session('filter_campaign'));
		} 
		
		if(session('filter_staff'))
		{       
			$data->where('lead_assigned_users',session('filter_staff'));
		}
		 
		 $data->whereDate('next_followup_date', '!=', date('Y-m-d') );
		// if(session()->get('key'))
		//{
			   //$data->whereDate('next_followup_date', '=', date('Y-m-d') );
		// }
	
		if ($colName != '' && $sort != '') 
		{
            $data->orderBy($colName, $sort);
        } 
		else 
		{
           $data->orderBy('tbl_lead_followup.next_followup_date', 'ASC');
        }
		
		$datas = ["iTotalDisplayRecords" => $data->count(), "iTotalRecords" => $data->count(), "TotalDisplayRecords" => $limit];
		$dataMod = $data->skip($offset)->take($limit)->get();
		$datas['data'] = $dataMod->toArray();
		return response()->json($datas);
    }
	
	/**********************************/
	public function datatableFollowup(Request $request)
    {
		$current_route = \Route::current()->uri();
		$privilege = Auth::user()->previlage;		
        $foll_id = $request->id;       
 		$limit = ($request->length != '') ? $request->length : 10;
		$offset = ($request->start != '') ? $request->start : 0;
		$search = $request->search['value'];
		
        $area  = FollowupModel::where('followup_status',0)
			->select('tbl_lead_followup.*', 'u1.name as name1', 'u2.name as name2')
			//->where('followup_branch_id',session('application_branch'))
            ->orderBy('followup_lead_id','desc')
            ->where('followup_lead_id',$foll_id)
            //->where('followup_reg_id',session('followup_reg_id'))
            // ->leftjoin('tbl_lead','tbl_lead.lead_reg_id','tbl_lead_followup.followup_reg_id')
            // ->where('tbl_lead.lead_status',0)
			->leftjoin('users as u1', 'followup_created', '=', 'u1.id') // ['created_by']
    		->leftjoin('users as u2', 'followup_assigned_users_id', '=', 'u2.id') // ['modified_by']
			//->leftjoin('users','users.id','followup_created')
            ->Where(function($query) use ($search) 
                {
                    //$query->where('course_name', 'like', $search . '%');
                   
                });
		if(Auth::user()->previlage != 2)
        { 
			$area = $area->where('followup_branch_id',session('application_branch'));
		}	
		$data = ["iTotalDisplayRecords"=>$area->count(),"iTotalRecords"=> $area->count(),"TotalDisplayRecords" =>$limit];
		$dataMod = $area->skip($offset)->take($limit)->get();
		$data['data'] = $dataMod->toArray();

		return response()->json($data);
    }
	/**********************************/
	
	public function leadAssign_mail($o_email,$content,$mailhead) 
    {   
        Mail::send('mailtemplate', $content, function($message)use($o_email,$mailhead) 
        {  
            $message->to($o_email);
            //$message->cc($cc_email);
            $message->subject($mailhead);
            $message->from('admin@auto-assure.com','Auto Assure');  /** From mail **/
        });
    }
	
	/**********************************/
    public function add_followupAction(Request $request)
	{   
		/*$validated = $request->validate([
				'status' => 'required',
			]);*/
	
        $unq_id = '';
		$oldstatus ='';
        $lead_data = LeadsModel::where('lead_id',$request->id)
				->where('lead_status',0)
				->latest()->first();
				//->where('lead_branch_id',session('application_branch'))
			
		if(Auth::user()->previlage != 2)
        {
			$lead_data->where('lead_branch_id',session('application_branch'));
		}
			
        if($lead_data)
		{
			$unq_id = $lead_data->lead_unq_id;
			$oldstatus = $lead_data->lead_assigned_status;
        }
         
        foreach ($lead_data as $key => $value) 
		{
           $fdata = FollowupModel::where('followup_reg_id',$lead_data->lead_reg_id)
			   		->where('followup_status',0)
					->where('followup_branch_id',session('application_branch'))
			   	    ->latest()->first();
			//if(Auth::user()->previlage != 2)
        	//{
				// $fdata->where('followup_branch_id',session('application_branch'));
			//}
		}
		
		if($request->status =='5') // Rejected
		{
			$assinged_user = $request->staff;
		}
        if($request->status == '6') // close
		{
			$assinged_user =$request->staff;
		}    
         
		if($request->status=='1') // Assign
        { 
            $assinged_user = $request->staff;
			
			$users = DB::table('users')
				->select('username','mobile','user_email')
				->where('id',$assinged_user)
				->where('status',0)
				->first();   
				
			$name   = $users->username;
			$mobile = $users->mobile;
			$email  = $users->user_email;   
			$msg = "New Lead Assigned to you.";
			
			/*********  Mail Start *********/	
			$o_email  = $email; //"admin@auto-assure.com";
            $content  = array('name'=>$name,'mobile'=>$mobile,'email'=>$email,'msg'=>$msg);
            $mailhead = "New Lead Assign";
            $this->leadAssign_mail($o_email,$content,$mailhead);
            /*********  Mail End *********/
        }
        else if($request->status=='2') // Reassign
        {
            $assinged_user = $request->staff;
            $assinged_user = $request->staff;
			
			$users = DB::table('users')
				->select('username','mobile','user_email')
				->where('id',$assinged_user)
				->where('status',0)
				->first();   
				
			$name   = $users->username;
			$mobile = $users->mobile;
			$email  = $users->user_email;   
			$msg = "New Lead Assigned to you.";
			
			/*********  Mail Start *********/	
			$o_email  = $email; //"admin@auto-assure.com";
            $content  = array('name'=>$name,'mobile'=>$mobile,'email'=>$email,'msg'=>$msg);
            $mailhead = "New Lead Reassigned";
            $this->leadAssign_mail($o_email,$content,$mailhead);
            /*********  Mail End *********/
            
        }
		else if($request->status=='15') // Plan / Shedule
        {
			if($request->staff)
			{
				$assinged_user = $request->staff;
			}
			else
			{
				$assinged_user = $lead_data->lead_assigned_users;
			}   
			$convertstatus = $request->status;
			$convertname   = $request->name;
        }
		else if($request->status=='16') // Reshedule
        {
			if($request->staff)
			{
				$assinged_user = $request->staff;
			}
			else
			{
				$assinged_user = $lead_data->lead_assigned_users;
			}
            
			$convertstatus = $request->status;
			$convertname   = $request->name;
        }
		
		else if($request->status=='14') // Inspection
        {
            $assinged_user = $lead_data->lead_assigned_users;
			$convertstatus = $request->status;
			$convertname   = $request->name;
        }
		else if($request->status=='18') // Inspection
        {
            $assinged_user = $lead_data->lead_assigned_users;
			$convertstatus = $request->status;
			$convertname   = $request->name;
        }
		
		else if($request->status=='17') // Approved
        {
            $assinged_user = $lead_data->lead_assigned_users;
			$convertstatus = $request->status;
			$convertname   = $request->name;
        }
		
		else if($request->status=='18') // Inspection Completed
        {
            $assinged_user = $lead_data->lead_assigned_users;
			$convertstatus = $request->status;
			$convertname   = $request->name;
        }
		
		/**else if($request->status=='4')
        {
            $assinged_user = $lead_data->lead_assigned_users;
			$convertstatus = $request->status;
			$convertname   = $request->name;
        }**/
        else
        {
            $assinged_user =" ";
        }
        
		if($request->date == null)
		{
			$next_followup_date = date('Y-m-d');
		}
		else
		{
			$next_followup_date = $request->date;
		}
		
        $data = ['followup_ip'      => $request->ip(),
                'followup_on'       => date('Y-m-d H:i:s'),
                'followup_status'   => 0,
                'followup_created'  => Auth::user()->id,
                'followup_date'     => date('Y-m-d'),
				'followup_date_on'  => date('Y-m-d'),
				'followup_time_on'  => date('H:i:s'),	 
                'followup_branch_id'=> auth::user()->user_branch,
                'followup_lead_id'  => $request->id,
                'followup_remarks'  => $request->remarks,
                'next_followup_date'=> $next_followup_date, 
				'followup_type_id'  => $request->status,
                'followup_current_status'    => $request->name,
                'followup_assigned_users_id' => $assinged_user,                
                'followup_reg_id'   => $lead_data->lead_reg_id,
                'followup_branch_id'=> session('application_branch')
                ];

		if($request->status=='1' || $request->status=='2') 
		{         
			$validator = Validator::make($request->all(),[
				'status' => 'required', 'staff'=> 'required', ]);
		}
		else
		{
     		$validator = Validator::make($request->all(),[ 'status' => 'required', ]);
		}
		
       	if ($validator->fails()) 
		{  
			foreach(array_values($validator->messages()->toArray()) as $msg) 
			{ 
				$error = implode(' ', $msg);
			}
			
			return response()->json(['status' => 1, 'text'=> $error, 'heading' => 'Warning']);
		} 
		else 
		{   
			$followup_id = FollowupModel::insertGetId($data);
			
			
			############## REPORT TABLE INSERT START ##############
			$basicReg = DB::table('tbl_basic_registration')
					->where('breg_id',$lead_data->lead_reg_id)
					->where('breg_status',0)
					->first();
 			
			if($request->status=='14') 
			{
				$check = DB::table('tbl_report')
					//->where('report_lead_id',$lead_data->lead_reg_id)
					->where('report_lead_id',$request->id)
					->where('report_status',0)
					->get();
				
				$count = count($check);
				if($count == 0)
				{
					$reportData = [						
						'report_ip'        => $request->ip(),
						'report_date'      => date('Y-m-d'),
						'report_time'      => date('H:i:s'),
						'report_addedby'   => Auth::user()->id,
						'report_status'    => 0, 
						
						'report_reg_id'     => $lead_data->lead_reg_id,
						'report_lead_id'    => $lead_data->lead_id,
						'report_followup_id'=> $followup_id,
						
						'report_reference_no'      => $lead_data->lead_unq_id, // Lead Unique id				
						'report_client_name'       => $basicReg->breg_fname,   				
						'report_client_name_ar'    => $basicReg->breg_fname_ar,   				
						'report_date_of_inspection'=> date('Y-m-d'), 				
						'report_followup_type'     => $convertstatus,
						'report_convertstatus'     => $convertname,
						
						'report_vehicle_plate_no'  => $lead_data->lead_vehicle_plate_no,
					]; 
			
				$report_id = DB::table('tbl_report')->insertGetId($reportData);
				
				$idColumn = $report_id;
				$dateCode = date('ym');
				$report_unique_id   = 'INSR'.$dateCode. str_pad($idColumn, 5, '0', STR_PAD_LEFT);
				//$report_unique_id = 'INSR'.str_pad($report_id,5,'0',STR_PAD_LEFT);

				DB::table('tbl_report')
					->where('report_id',$report_id)
					->update(['report_unique_id' => $report_unique_id]);
				}
			}
			
			if($request->status=='18') 
			{
			    $checkreport = DB::table('tbl_report')
					->where('report_lead_id',$request->id)
					->where('report_status',0)
					->first();
					
			    $reportId = $checkreport->report_id;
				if($checkreport)
				{
				    $randomString = Str::random(10);
    				DB::table('tbl_report')
    					->where('report_id',$reportId)
    					->update(['report_unique_id_random' => $randomString]);
				}   
			}
			################ REPORT TABLE INSERT END ################
         
			if($request->status=='1') // Assign
			{
				LeadsModel::where('lead_id',$lead_data->lead_id)->update(['lead_assigned_users'=>$assinged_user,'lead_assigned_status'=>$request->name,'lead_followup_type'=>$request->status,'lead_followupcreated'=>Auth::user()->id]);
			}
			else if($request->status=='2') // Reassign
			{
				LeadsModel::where('lead_id',$lead_data->lead_id)->update(['lead_assigned_users'=>$assinged_user,'lead_assigned_status'=>$request->name,'lead_followup_type'=>$request->status,'lead_followupcreated'=>Auth::user()->id]);
			}
			else if($request->status=='3') // Followup 
			{
				LeadsModel::where('lead_id',$lead_data->lead_id)->update(['lead_assigned_users'=>$assinged_user,'lead_assigned_status'=>$request->name,'lead_followup_type'=>$request->status,'lead_followupcreated'=>Auth::user()->id]);
			}
			else if($request->status=='4') // Register
			{
				LeadsModel::where('lead_id',$lead_data->lead_id)->update(['lead_assigned_users'=>$assinged_user,'lead_assigned_status'=>$request->name,'lead_followup_type'=>$request->status,'lead_followupcreated'=>Auth::user()->id]);
			}
			else if($request->status=='5') // Rejected 
			{            
				LeadsModel::where('lead_id',$lead_data->lead_id)->update(['lead_assigned_users'=>0,'lead_assigned_status'=>$request->name,'lead_followup_type'=>$request->status,'lead_followupcreated'=>Auth::user()->id]);      
			}
			else if($request->status=='6') // Closed
			{            
				LeadsModel::where('lead_id',$lead_data->lead_id)->update(['lead_assigned_users'=>0,'lead_assigned_status'=>$request->name,'lead_followup_type'=>$request->status,'lead_followupcreated'=>Auth::user()->id]);      
			}
			
			else if($request->status=='14') // Inspection
			{            
				LeadsModel::where('lead_id',$lead_data->lead_id)->update(['lead_assigned_users'=>$assinged_user,'lead_assigned_status'=>$request->name,'lead_followup_type'=>$request->status,'lead_followupcreated'=>Auth::user()->id]);      
			}
			else if($request->status=='15') // Plan / Shedule
			{            
				LeadsModel::where('lead_id',$lead_data->lead_id)->update(['lead_assigned_users'=>$assinged_user,'lead_assigned_status'=>$request->name,'lead_followup_type'=>$request->status,'lead_followupcreated'=>Auth::user()->id]);      
			}
			else if($request->status=='16')  // Reshedule
			{            
				LeadsModel::where('lead_id',$lead_data->lead_id)->update(['lead_assigned_users'=>$assinged_user,'lead_assigned_status'=>$request->name,'lead_followup_type'=>$request->status,'lead_followupcreated'=>Auth::user()->id]);      
			}
			else if($request->status=='17')  // Approved
			{            
				LeadsModel::where('lead_id',$lead_data->lead_id)->update(['lead_assigned_users'=>0,'lead_assigned_status'=>$request->name,'lead_followup_type'=>$request->status,'lead_followupcreated'=>Auth::user()->id]);      
			}
			else if($request->status=='18')  // Inspection completed
			{            
				LeadsModel::where('lead_id',$lead_data->lead_id)->update(['lead_assigned_users'=>$assinged_user,'lead_assigned_status'=>$request->name,'lead_followup_type'=>$request->status,'lead_followupcreated'=>Auth::user()->id]);      
			}
			
			$ip = $request->ip();
			$action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $category = "New Followup";
            $activity = 'New Followup for lead # '.strip_tags($unq_id).' Has been Added By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);
				
        	return response()->json(['status' => 0,'heading'=>'Success','text'=>'Followup Added Successfully','icon'=>'success']);
		}
   }
	
}