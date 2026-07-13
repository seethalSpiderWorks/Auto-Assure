<?php

namespace Modules\InspectionReport\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\Core;
use Carbon\Carbon;
use DB;
use Image; 
 
class InspectionReportController extends Controller
{	
    public function index()
    {
		$exte_color = DB::table('tbl_exterior_color')
				->select('exte_color_id','exte_color_name')                        
				->where('exte_color_status',0)
				->where('exte_color_publish_status',1)
				->get();
 	
		$inte_color = DB::table('tbl_interior_color')
				->select('inte_color_id','inte_color_name')                        
				->where('inte_color_status',0)
				->where('inte_color_publish_status',1)
				->get();
 
		$gear_box = DB::table('tbl_gearbox_type')
				->select('gearbox_type_id','gearbox_type_name')                        
				->where('gearbox_type_status',0)
				//->where('gearbox_type_publish_status', 2)    
				->get();
 
		$fuel_type = DB::table('tbl_fuel_type')
				->select('fuel_type_id','fuel_type_name')                        
				->where('fuel_type_status',0)
				//->where('fuel_type_publish_status',2)
				->get();
		
		$steer_side = DB::table('tbl_steering_side')
				->select('steering_side_id','steering_side_name')                        
				->where('steering_side_status',0)
				->get();	

		$summ_type = DB::table('tbl_summary_type')
				->select('summary_type_id','summary_type_name')                        
				->where('summary_type_status',0)
				->get();
 
		$gall_type = DB::table('tbl_gallery_type')
				->select('gallery_type_id','gallery_type_name')                        
				->where('gallery_type_status',0)
				->get();
 
		$summ_desc = DB::table('tbl_summary_description')
				->select('sum_desc_id','sum_desc_type','sum_desc_name','sum_desc_name_ar')
				->where('sum_desc_status',0)
				->get();
		
        return view('inspectionreport::index')->with(['exte_color'=>$exte_color,'inte_color'=>$inte_color,'gear_box'=>$gear_box,'fuel_type'=>$fuel_type,'steer_side'=>$steer_side,'summ_type'=>$summ_type,'gall_type'=>$gall_type,'summ_desc'=>$summ_desc]);
    }
	
	/********** Datatable **********/
	public function getDatatable(Request $request)
	{
		ini_set('memory_limit', '-1');        
        $current_route = \Route::current()->uri();
		
		$privilege = Auth::user()->previlage;
		$main_id = 45;
		$sub_id = 100;
		$option = DB::table('tbl_menu_set_options')
				->select('opset_options')
				->where('opset_privilege',$privilege)
				->where('opset_main_id',$main_id)
				->where('opset_sub_id',$sub_id)
				->first();

		$limit  = ($request->length != '') ? $request->length : 10;
		$offset = ($request->start != '') ? $request->start : 0;
		$search =  $request->search['value'];

		$user_id = Auth::user()->user_id;
		 
		$area = DB::table('tbl_report')
			->select('report_id','report_reference_no','report_client_name','report_date_of_inspection','report_expired_status','users.name','tbl_lead.lead_id','tbl_lead.lead_followup_type','lead_assigned_status')
			->where('report_status',0)   
            ->leftjoin('users', 'users.id' ,'=','tbl_report.report_addedby')
            ->leftjoin('tbl_lead', 'tbl_lead.lead_id' ,'=','tbl_report.report_lead_id')
            ->orderBy('tbl_report.report_id','desc')
            ->Where(function($query) use ($search) 
                    {
						$query->where('report_reference_no', 'like', $search . '%');
						$query->orwhere('report_client_name', 'like', $search . '%');
						$query->orwhere('lead_assigned_status', 'like', $search . '%');
						$query->orwhere('report_expired_status', 'like', $search . '%');
						$query->orwhere('name', 'like', $search . '%');
                    });
 	  
		if($privilege != 1 && $privilege != 2 && $privilege != 48)
		{
			$area->where('report_addedby',Auth::user()->id);
		}
	 
		if($privilege == 48)
		{   
			$area->where('lead_added_by',Auth::user()->id);
		}
		
		$data = ["iTotalDisplayRecords" => $area->count(), "iTotalRecords" => $area->count(), "TotalDisplayRecords" => $limit,'option'=> $option];
		$dataMod = $area->skip($offset)->take($limit)->get();  
		$data['data'] = $dataMod->toArray();  
		return response()->json($data);
	}
	
	/********** GeneralInfo **********/
	public function addGeneralInfo(Request $request)
    {
        $reference_no       = $request->report_reference_no; 
        $client_name        = $request->report_client_name;
        $client_name_ar     = $request->report_client_name_ar;
        $date_of_inspection = $request->report_date_of_inspection;
        $edit_id            = $request->edit_id;   
		
        $data = array(
				'report_ip'      => $request->ip(),
				'report_addedby' => Auth::user()->id,
                'report_status'  => 0, 
				'report_reference_no'   => $reference_no, 				
				'report_client_name'    => $client_name, 				
				'report_client_name_ar' => $client_name_ar, 				
				'report_date_of_inspection'=> $date_of_inspection, 				
                );
         
		if($edit_id == '' || $edit_id == null)
		{   
			$insertid = DB::table('tbl_report')->insertGetId($data);
			$cYear  = date('y');
			$cMonth = date('m');
            //$order_uniqueid = 'U'.$cYear.$cMonth.'LI'.str_pad($insertid,4,'0',STR_PAD_LEFT);
            //$uniqueid =  DB::table('tbl_report')->where('report_id',$insertid)->update(['list_unq_id'=>$order_uniqueid]);
		}
		else 
		{ 
			$data['report_editedby'] = Auth::user()->id;
			$update = DB::table('tbl_report')
				->where('report_id',$edit_id)
				->update($data);
			$insertid = $edit_id;
		}
 
		if($insertid)
		{
			return response()->json(['status'=> 1, 'msg' => 'Report Details Added Successfully', 'heading' => 'Success','id'=>$insertid]);
		}
		else
		{
			return response()->json(['status' => 0, 'msg' => 'Error !try again', 'heading' => 'Error']);
		}
    }
		
	/********** Vehicle **********/
	public function add_vehicleInfo(Request $request)
    {  
        $edit_id   = $request->edit_id; /** report_id for insert other tables **/   
        $update_id = $request->update_id; /** report_id for edit **/    
	 
        $data = array(
				'vehicle_info_status'     => 0, 
				'vehicle_info_addedby'    => Auth::user()->id,
				'vehicle_info_ip'         => $request->ip(),
				'vehicle_info_report_id'  => $edit_id,
				'vehicle_info_title'      => $request->vehicle_info_title,			
				'vehicle_info_model_year' => $request->vehicle_info_model_year,				
				'vehicle_info_manuf_year' => $request->vehicle_info_manuf_year,		
				'vehicle_info_chassis_no' => $request->vehicle_info_chassis_no,		
				'vehicle_info_odometer'   => $request->vehicle_info_odometer,		
				'vehicle_info_condition'  => $request->vehicle_info_condition,		
                );
				
		$addSpec = array(
				'add_spec_status'         => 0, 
				'add_spec_addedby'        => Auth::user()->id,
				'add_spec_ip'             => $request->ip(),
				'add_spec_report_id'      => $edit_id, 
				'add_spec_region'         => $request->add_spec_region,			
				'add_spec_exterior_color' => $request->add_spec_exterior_color,			
				'add_spec_interior_color' => $request->add_spec_interior_color,			
				'add_spec_gearbox'        => $request->add_spec_gearbox,			
				'add_spec_fuel_type'      => $request->add_spec_fuel_type,			
				'add_spec_steering_side'  => $request->add_spec_steering_side,			
				'add_spec_cylinders'      => $request->add_spec_cylinders,			
				'add_spec_engine_size'    => $request->add_spec_engine_size,			
				'add_spec_keys'           => $request->add_spec_keys,			
				'add_spec_doors'          => $request->add_spec_doors,			
				'add_spec_seats'          => $request->add_spec_seats,			
                );
				
		$addWarr = array(
				'war_service_status'   => 0, 
				'war_service_addedby'  => Auth::user()->id,
				'war_service_ip'       => $request->ip(),
				'war_service_report_id'=> $edit_id, 
				'war_service_history'  => $request->war_service_history,			
				'war_service_last'     => $request->war_service_last,			
				'war_service_next'     => $request->war_service_next,			
                );
          
		if($update_id == '' || $update_id == null)
		{
			$veh_infoId = DB::table('tbl_report_vehicle_info')->insertGetId($data);
			$add_specId = DB::table('tbl_report_additionl_spec')->insertGetId($addSpec);
			$warrantyId = DB::table('tbl_report_warranty_service')->insertGetId($addWarr);
			$insertid   = $edit_id;
 		}
		else 
		{	 
			$deleteid = DB::table('tbl_report_vehicle_info')->where('vehicle_info_report_id',$edit_id)->delete();
			$data['vehicle_info_editedby'] = Auth::user()->id;
			$update = DB::table('tbl_report_vehicle_info')
					->where('vehicle_info_report_id',$edit_id)
					->insert($data); //->update($data);
					
			$deleteSpec = DB::table('tbl_report_additionl_spec')->where('add_spec_report_id',$edit_id)->delete();	
			$addSpec['add_spec_editedby'] = Auth::user()->id;
			$updateSpec = DB::table('tbl_report_additionl_spec')
				->where('add_spec_report_id',$edit_id)
				->insert($addSpec); //->update($addSpec);
				
			$delWarrid = DB::table('tbl_report_warranty_service')->where('war_service_report_id',$edit_id)->delete();	
			$addWarr['war_service_editedby'] = Auth::user()->id;
			$updateWarr = DB::table('tbl_report_warranty_service')
					->where('war_service_report_id',$edit_id)
					->insert($addWarr); //->update($addWarr);
			
			$insertid = $edit_id;
		}

		if($insertid)
		{
			return response()->json(['status' => 1, 'msg' => 'Vehicle Details Added Successfully', 'heading' => 'Success','id'=>$insertid]);
		}
		else
		{
			return response()->json(['status' => 0, 'msg' => 'Error !try again', 'heading' => 'Error']);
		}
    }
	
	/********** Additional Specification **********/
	public function add_additionalSpec(Request $request)
    {
        $edit_id   = $request->edit_id; /** report_id for insert other tables **/   
        $update_id = $request->update_id; /** report_id for edit **/    

        $data = array(
				'add_spec_status'         => 0, 
				'add_spec_addedby'        => Auth::user()->id,
				'add_spec_ip'             => $request->ip(),
				'add_spec_report_id'      => $edit_id, 
				'add_spec_region'         => $request->add_spec_region,			
				'add_spec_exterior_color' => $request->add_spec_exterior_color,			
				'add_spec_interior_color' => $request->add_spec_interior_color,			
				'add_spec_gearbox'        => $request->add_spec_gearbox,			
				'add_spec_fuel_type'      => $request->add_spec_fuel_type,			
				'add_spec_steering_side'  => $request->add_spec_steering_side,			
				'add_spec_cylinders'      => $request->add_spec_cylinders,			
				'add_spec_engine_size'    => $request->add_spec_engine_size,			
				'add_spec_keys'           => $request->add_spec_keys,			
				'add_spec_doors'          => $request->add_spec_doors,			
				'add_spec_seats'          => $request->add_spec_seats,			
            );
         
		if($update_id == '' || $update_id == null)
		{
			$insertid = DB::table('tbl_report_additionl_spec')->insertGetId($data);
			$insertid = $edit_id;
 		}
		else 
		{  
			$deleteid = DB::table('tbl_report_additionl_spec')->where('add_spec_report_id',$edit_id)->delete();	
			$data['add_spec_editedby'] = Auth::user()->id;
			$update = DB::table('tbl_report_additionl_spec')
				->where('add_spec_report_id',$edit_id)
				->insert($data); //->update($data);
				
			$insertid = $edit_id;
		}

		if($insertid)
		{
			return response()->json(['status' => 1, 'msg' => 'Additional Specification Added Successfully', 'heading' => 'Success','id'=>$insertid]);
		}
		else
		{
			return response()->json(['status' => 0, 'msg' => 'Error !try again', 'heading' => 'Error']);
		}
    }
	
	/********** Warranty / Services **********/
	public function add_warrantyServices(Request $request)
    {
        $edit_id   = $request->edit_id; /** report_id for insert other tables **/   
        $update_id = $request->update_id; /** report_id for edit **/    
		
        $data = array(
				'war_service_status'   => 0, 
				'war_service_addedby'  => Auth::user()->id,
				'war_service_ip'       => $request->ip(),
				'war_service_report_id'=> $edit_id, 
				'war_service_history'  => $request->war_service_history,			
				'war_service_last'     => $request->war_service_last,			
				'war_service_next'     => $request->war_service_next,			
                );
          
		if($update_id == '' || $update_id == null)
		{
			$insertid = DB::table('tbl_report_warranty_service')->insertGetId($data);
			$insertid = $edit_id;
 		}
		else 
		{
			$deleteid = DB::table('tbl_report_warranty_service')->where('war_service_report_id',$edit_id)->delete();	
			$data['war_service_editedby'] = Auth::user()->id;
			$update = DB::table('tbl_report_warranty_service')
					->where('war_service_report_id',$edit_id)
					->insert($data); //->update($data);
			
			$insertid = $edit_id;
		}
    
		if($insertid)
		{
			return response()->json(['status' => 1, 'msg' => 'Warranty / Services Added Successfully', 'heading' => 'Success','id'=>$insertid]);
		}
		else
		{
			return response()->json(['status' => 0, 'msg' => 'Error !try again', 'heading' => 'Error']);
		}
    }
	
	/********** Inspection Summary **********/
	public function add_inspectionSummary(Request $request)
    {
        $edit_id   = $request->edit_id; /** report_id for insert other tables **/   
        $update_id = $request->update_id; /** report_id for edit **/    
		
		$extra_type = $request->extra_type;
          
		if($edit_id == '')
		{
			/****** INSERT ******/
			if($extra_type[0] != null)
			{
				$tab_name  = $request->extra_type;
				$count_int = count($tab_name);
				
				if($count_int>0)
				{
					for ($i=0; $i<$count_int ; $i++) 
					{  
						$desc_en = trim($request->extra_name[$i]); // English name
						$desc_ar = trim($request->extra_name_ar[$i]); // Arabic name (optional)
						// Check if it already exists
						$existing = DB::table('tbl_summary_description')
							->where('sum_desc_name', $desc_en)
							->where('sum_desc_status', 0)
							->first();
							
						// If it doesn't exist, insert it into tbl_summary_description
						if (!$existing && $desc_en != '') {
							$desc_id = DB::table('tbl_summary_description')->insertGetId([
								'sum_desc_name'    => $desc_en,
								'sum_desc_name_ar' => $desc_ar ?? '',
								'sum_desc_type'    => $request->extra_type[$i],
								'sum_desc_status'  => 0,
								'sum_desc_date'    => date('Y-m-d'),
								'sum_desc_addedby' => Auth::user()->id,
								'sum_desc_ip'      => $request->ip(),
							]);
						}
						else {
							$desc_id = $existing->sum_desc_id ?? null;
						}
						
						// Always insert into tbl_report_insp_summary
						$insertid = DB::table('tbl_report_insp_summary')
							->insert([
								'insp_summary_report_id' => $edit_id,
								'insp_summary_status'  => '0',
								'insp_summary_ip'      => $request->ip(),
								'insp_summary_addedby' => Auth::user()->id,
								'insp_summary_type'    => $request->extra_type[$i],
								'insp_summary_desc'    => $desc_en,
								'insp_summary_desc_ar' => $desc_ar,
								'insp_summary_desc_id' => $desc_id,
							]);
					}
				}
			}
			/****** INSERT ******/
			$insertid = $edit_id;
 		}
		else 
		{
			/****** UPDATE ******/ 
			if($extra_type !='')
			{
                $tabname   = $request->extra_type;
            	$count_int = count($tabname);
            	
				if($count_int>=1)
				{
					$deleteclub = DB::table('tbl_report_insp_summary')
									->where('insp_summary_report_id', $edit_id)
									->where('insp_summary_status', 0)
									->update(['insp_summary_editedby'=>Auth::user()->id,
											'insp_summary_status' => 1]);
                        
					for($i=0; $i<$count_int ; $i++) 
					{
						$desc_en = trim($request->extra_name[$i]);
						$desc_ar = trim($request->extra_name_ar[$i] ?? '');
						
						//  Insert into tbl_summary_description if it's a new value
						$exists = DB::table('tbl_summary_description')
							->where('sum_desc_name', $desc_en)
							->where('sum_desc_status', 0)
							->first();

						if (!$exists && $desc_en != '') {
							$desc_ids = DB::table('tbl_summary_description')->insertGetId([
								'sum_desc_name'    => $desc_en,
								'sum_desc_name_ar' => $desc_ar,
								'sum_desc_type'    => $request->extra_type[$i],
								'sum_desc_status'  => 0,
								'sum_desc_date'    => date('Y-m-d'),
								'sum_desc_addedby' => Auth::user()->id,
								'sum_desc_ip'      => $request->ip(),
							]);
						}
						else {
							$desc_ids = $exists->sum_desc_id ?? null;
						}
			
						$inData = array(
								'insp_summary_report_id' => $edit_id,
								'insp_summary_status'  => '0',
								'insp_summary_ip'      => $request->ip(),
								'insp_summary_addedby' => Auth::user()->id,
								'insp_summary_type'    => $request->extra_type[$i],
								'insp_summary_desc'    => $desc_en,
								'insp_summary_desc_ar' => $desc_ar,
								'insp_summary_desc_id' => $desc_ids,
							);   
							
						if(isset($request->doc[$i]))
						{
							$d1 = $request->doc[$i];
							$previousData = DB::table('tbl_report_insp_summary')
                                    ->where('insp_summary_report_id',$d1)
                                    ->first();
						}
                  
						$club_names = DB::table('tbl_report_insp_summary')->insert([$inData]);
					}  
				}
			}	
			/****** UPDATE ******/ 
			$insertid = $edit_id;
		}
  
		if($insertid)
		{
			return response()->json(['status' => 1, 'msg' => 'Inspection Summary Added Successfully', 'heading' => 'Success','id'=>$insertid]);
		}
		else
		{
			return response()->json(['status' => 0, 'msg' => 'Error !try again', 'heading' => 'Error']);
		}
    }
	
	/********** Vehicle Overview **********/
	public function addVehicleOverview(Request $request) 
    {   
        $overview_english = $request->overview_english;
        $overview_arabic  = $request->overview_arabic;
        $edit_id          = $request->edit_id;      
        $update_id        = $request->update_id;      
		
        $data = array(
				'overview_ip'       => $request->ip(),
				'overview_addedby'  => Auth::user()->id,
                'overview_status'   => 0, 
				'overview_report_id'=> $edit_id, 
				'overview_english'  => $overview_english, 				
				'overview_arabic'   => $overview_arabic, 	
            ); 

		if($update_id == '' || $update_id == null)
		{
			$insertid = DB::table('tbl_report_overview')->insertGetId($data);
			$insertid = $edit_id;
 		}
		else 
		{
			$deleteid = DB::table('tbl_report_overview')->where('overview_report_id',$edit_id)->delete();	
			$data['overview_editedby'] = Auth::user()->id;
			$update = DB::table('tbl_report_overview')
					->where('overview_report_id',$edit_id)
					->insert($data); //->update($data);
			
			$insertid = $edit_id;
		}
		 
		if($insertid)
		{
			return response()->json(['status'=> 1, 'msg' => 'Vehicle Overview Added Successfully', 'heading' => 'Success','id'=>$insertid]);
		}
		else
		{
			return response()->json(['status' => 0, 'msg' => 'Error !try again', 'heading' => 'Error']);
		}
    }
	
	/********** VEHICLE SPECIFICATION  **********/
	public function add_vehicleSpecification(Request $request)
	{
        $edit_id   = $request->edit_id; /** report_id for insert other tables **/   
        $update_id = $request->update_id; /** report_id for edit **/    
		
		$dataOne = array(
				'one_report_id' => $edit_id, 
				'one_status'    => 0, 
				'one_addedby'   => Auth::user()->id,
				'one_ip'        => $request->ip(),
				'air_suspension'      => $request->air_suspension,	
				'adaptive_air_suspension' => $request->adaptive_air_suspension,	
				'differential_lock'   => $request->differential_lock,	
				'paddle_shifters'     => $request->paddle_shifters,	
				'tiptronic'           => $request->tiptronic,	
				'hill_descent_assist' => $request->hill_descent_assist,	
				'hill_start_assist'   => $request->hill_start_assist,
				'auto_hold'           => $request->auto_hold,
				'comfort_seats'       => $request->comfort_seats,
				'sport_seats'         => $request->sport_seats,
				'sport_brakes'        => $request->sport_brakes,
				'sport_suspension'    => $request->sport_suspension,
				'sport_exhaust'       => $request->sport_exhaust,
				'lane_change'         => $request->lane_change,
				'launch_control'      => $request->launch_control,	
				
				/** Safty **/
				'child_safety_seats'   => $request->child_safety_seats,	
				'front_view_camera'    => $request->front_view_camera,	
				'rear_view_camera'     => $request->rear_view_camera,	 
				'degree_camera'        => $request->degree_camera,	
				'front_parking_sensors'=> $request->front_parking_sensors,	
				'rear_parking_sensors' => $request->rear_parking_sensors,	
				
				'lane_departure'       => $request->lane_departure,	
				'anti_lock_brakes'     => $request->anti_lock_brakes,	
				'ebd'                  => $request->ebd,	
				'alarm'                => $request->alarm,	
				'front_airbags'        => $request->front_airbags,	
				'side_airbags'         => $request->side_airbags,	
				'traction_control_sys' => $request->traction_control_sys,	
				'park_assist'          => $request->park_assist,	
				'blind_spot_monitor'   => $request->blind_spot_monitor,	
				'tire_pressure_monitor'=> $request->tire_pressure_monitor,	
				'anti_glare_rear_view' => $request->anti_glare_rear_view,	
				
				/** Aftermarket Added Accessories **/
				'winch'                   => $request->winch,	
				'body_kit_aaa'            => $request->body_kit_aaa,	
				'lift_kit_aaa'            => $request->lift_kit_aaa,	
				'leather_seats_aaa'       => $request->leather_seats_aaa,	
				'rear_seat_enter_sys_aaa' => $request->rear_seat_enter_sys_aaa,	
				'parking_sensors'         => $request->parking_sensors,	
				'rear_view_camera_aaa'    => $request->rear_view_camera_aaa,	
				'navigation_aaa'          => $request->navigation_aaa,	
				'fire_extinguisher'       => $request->fire_extinguisher,
				
				/**** Comments ****/
				'air_suspension_cmnt'      => $request->air_suspension_cmnt,	
				'adaptive_air_suspension_cmnt' => $request->adaptive_air_suspension_cmnt,	
				'differential_lock_cmnt'   => $request->differential_lock_cmnt,	
				'paddle_shifters_cmnt'     => $request->paddle_shifters_cmnt,	
				'tiptronic_cmnt'           => $request->tiptronic_cmnt,	
				'hill_descent_assist_cmnt' => $request->hill_descent_assist_cmnt,	
				'hill_start_assist_cmnt'   => $request->hill_start_assist_cmnt,
				'auto_hold_cmnt'           => $request->auto_hold_cmnt,
				'comfort_seats_cmnt'       => $request->comfort_seats_cmnt,
				'sport_seats_cmnt'         => $request->sport_seats_cmnt,
				'sport_brakes_cmnt'        => $request->sport_brakes_cmnt,
				'sport_suspension_cmnt'    => $request->sport_suspension_cmnt,
				'sport_exhaust_cmnt'       => $request->sport_exhaust_cmnt,
				'lane_change_cmnt'         => $request->lane_change_cmnt,
				'launch_control_cmnt'      => $request->launch_control_cmnt,	
				
				/** Safty **/
				'child_safety_seats_cmnt'   => $request->child_safety_seats_cmnt,	
				'front_view_camera_cmnt'    => $request->front_view_camera_cmnt,	
				'rear_view_camera_cmnt'     => $request->rear_view_camera_cmnt,	 
				'degree_camera_cmnt'        => $request->degree_camera_cmnt,	
				'front_parking_sensors_cmnt'=> $request->front_parking_sensors_cmnt,	
				'rear_parking_sensors_cmnt' => $request->rear_parking_sensors_cmnt,	
				
				'lane_departure_cmnt'       => $request->lane_departure_cmnt,	
				'anti_lock_brakes_cmnt'     => $request->anti_lock_brakes_cmnt,	
				'ebd_cmnt'                  => $request->ebd_cmnt,	
				'alarm_cmnt'                => $request->alarm_cmnt,	
				'front_airbags_cmnt'        => $request->front_airbags_cmnt,	
				'side_airbags_cmnt'         => $request->side_airbags_cmnt,	
				'traction_control_sys_cmnt' => $request->traction_control_sys_cmnt,	
				'park_assist_cmnt'          => $request->park_assist_cmnt,	
				'blind_spot_monitor_cmnt'   => $request->blind_spot_monitor_cmnt,	
				'tire_pressure_monitor_cmnt'=> $request->tire_pressure_monitor_cmnt,	
				'anti_glare_rear_view_cmnt' => $request->anti_glare_rear_view_cmnt,	
				
				/** Aftermarket Added Accessories **/
				'winch_cmnt'                   => $request->winch_cmnt,	
				'body_kit_aaa_cmnt'            => $request->body_kit_aaa_cmnt,	
				'lift_kit_aaa_cmnt'            => $request->lift_kit_aaa_cmnt,	
				'leather_seats_aaa_cmnt'       => $request->leather_seats_aaa_cmnt,	
				'rear_seat_enter_sys_aaa_cmnt' => $request->rear_seat_enter_sys_aaa_cmnt,	
				'parking_sensors_cmnt'         => $request->parking_sensors_cmnt,	
				'rear_view_camera_aaa_cmnt'    => $request->rear_view_camera_aaa_cmnt,	
				'navigation_aaa_cmnt'          => $request->navigation_aaa_cmnt,	
				'fire_extinguisher_cmnt'       => $request->fire_extinguisher_cmnt,		
				);
				
		$dataTwo = array(
				'two_report_id' => $edit_id, 
				'two_status'    => 0, 
				'two_addedby'   => Auth::user()->id,
				'two_ip'        => $request->ip(),
				/** Interior - Entertainment **/
				'digital_driver_display'=> $request->digital_driver_display,	
				'cd_player'            => $request->cd_player,	
				'dvd_player'           => $request->dvd_player,	
				'mp_player'            => $request->mp_player,	
				'sd_card_player'       => $request->sd_card_player,
				'bluetooth_interface'  => $request->bluetooth_interface,
				'premium_sound_system' => $request->premium_sound_system,				
				'aux_audio_system'     => $request->aux_audio_system,	
				'usb'                  => $request->usb,	
				'usb_c'                => $request->usb_c,	
				'touch_screen'         => $request->touch_screen,	
				'rear_seat_enter_sys'  => $request->rear_seat_enter_sys,	
				'wireless'             => $request->wireless,	
				'ambient_lighting'     => $request->ambient_lighting,	
				'apple_carplay'        => $request->apple_carplay,	
				'navigation'           => $request->navigation,	
				'standard_ac'          => $request->standard_ac,	
				'dual_climcont_ac'     => $request->dual_climcont_ac,	
				'multi_climcont_ac'    => $request->multi_climcont_ac,	
				'keyless_entry'        => $request->keyless_entry,	
				'keyless_start'        => $request->keyless_start,	
				'power_steering'       => $request->power_steering,	
				'heads_up_display'     => $request->heads_up_display,	
				'cruise_control'       => $request->cruise_control,	
				'adaptive_cruise_control' => $request->adaptive_cruise_control,	
				
				'seat_cooling_front'   => $request->seat_cooling_front,	
				'seat_cooling_rear'    => $request->seat_cooling_rear,	
				'seat_massage_front'   => $request->seat_massage_front,	
				'seat_massage_rear'    => $request->seat_massage_rear,	
				'driver_memory_seat'   => $request->driver_memory_seat,	
				'passenger_memory_seat'=> $request->passenger_memory_seat,	
				'power_driver_seats'   => $request->power_driver_seats,	
				'power_passenger_seats'=> $request->power_passenger_seats,	
				'power_rear_seats'     => $request->power_rear_seats,	
				'power_front_windows'  => $request->power_front_windows,	
				'power_rear_windows'   => $request->power_rear_windows,	
				'power_trunk'          => $request->power_trunk,	
				'power_locks'          => $request->power_locks,	
				'power_mirrors'        => $request->power_mirrors,	
				'power_folding_mirrors'=> $request->power_folding_mirrors,	
				'sun_roof'             => $request->sun_roof,	
				'panoramic_roof'       => $request->panoramic_roof,	
				'cool_box'             => $request->cool_box,	
				'seat_heated_front'    => $request->seat_heated_front,	
				'auto_park'            => $request->auto_park,	
				'remote_start_engine'  => $request->remote_start_engine,	
				'soft_close_doors'     => $request->soft_close_doors,	
				'adaptive_lights'      => $request->adaptive_lights,	
				'night_vision'         => $request->night_vision,	
				'captain_rear_seats'   => $request->captain_rear_seats,	
				'leather_seats'        => $request->leather_seats,	
				'leather_fabric'       => $request->leather_fabric,	
				'body_kit'             => $request->body_kit,	
				'lift_kit'             => $request->lift_kit,	
				'front_spoiler'        => $request->front_spoiler,	
				'rear_spoiler'         => $request->rear_spoiler,	
				'fog_light_front'      => $request->fog_light_front,	
				'roof_carrier'         => $request->roof_carrier,	
				'halogen_headlight'    => $request->halogen_headlight,	
				'led_headlight'        => $request->led_headlight,	
				'xenon_headlight'      => $request->xenon_headlight,	
				'trailer_hook_coupling'=> $request->trailer_hook_coupling,

				/**** Comments ****/
				/** Interior - Entertainment **/
				'digital_driver_display_cmnt'=> $request->digital_driver_display_cmnt,	
				'cd_player_cmnt'            => $request->cd_player_cmnt,	
				'dvd_player_cmnt'           => $request->dvd_player_cmnt,	
				'mp_player_cmnt'            => $request->mp_player_cmnt,	
				'sd_card_player_cmnt'       => $request->sd_card_player_cmnt,
				'bluetooth_interface_cmnt'  => $request->bluetooth_interface_cmnt,
				'premium_sound_system_cmnt' => $request->premium_sound_system_cmnt,				
				'aux_audio_system_cmnt'     => $request->aux_audio_system_cmnt,	
				'usb_cmnt'                  => $request->usb_cmnt,	
				'usb_c_cmnt'                => $request->usb_c_cmnt,	
				'touch_screen_cmnt'         => $request->touch_screen_cmnt,	
				'rear_seat_enter_sys_cmnt'  => $request->rear_seat_enter_sys_cmnt,	
				'wireless_cmnt'             => $request->wireless_cmnt,	
				'ambient_lighting_cmnt'     => $request->ambient_lighting_cmnt,	
				'apple_carplay_cmnt'        => $request->apple_carplay_cmnt,	
				'navigation_cmnt'           => $request->navigation_cmnt,	
				'standard_ac_cmnt'          => $request->standard_ac_cmnt,	
				'dual_climcont_ac_cmnt'     => $request->dual_climcont_ac_cmnt,	
				'multi_climcont_ac_cmnt'    => $request->multi_climcont_ac_cmnt,	
				'keyless_entry_cmnt'        => $request->keyless_entry_cmnt,	
				'keyless_start_cmnt'        => $request->keyless_start_cmnt,	
				'power_steering_cmnt'       => $request->power_steering_cmnt,	
				'heads_up_display_cmnt'     => $request->heads_up_display_cmnt,	
				'cruise_control_cmnt'       => $request->cruise_control_cmnt,	
				'adaptive_cruise_control_cmnt' => $request->adaptive_cruise_control_cmnt,	
				
				'seat_cooling_front_cmnt'   => $request->seat_cooling_front_cmnt,	
				'seat_cooling_rear_cmnt'    => $request->seat_cooling_rear_cmnt,	
				'seat_massage_front_cmnt'   => $request->seat_massage_front_cmnt,	
				'seat_massage_rear_cmnt'    => $request->seat_massage_rear_cmnt,	
				'driver_memory_seat_cmnt'   => $request->driver_memory_seat_cmnt,	
				'passenger_memory_seat_cmnt'=> $request->passenger_memory_seat_cmnt,	
				'power_driver_seats_cmnt'   => $request->power_driver_seats_cmnt,	
				'power_passenger_seats_cmnt'=> $request->power_passenger_seats_cmnt,	
				'power_rear_seats_cmnt'     => $request->power_rear_seats_cmnt,	
				'power_front_windows_cmnt'  => $request->power_front_windows_cmnt,	
				'power_rear_windows_cmnt'   => $request->power_rear_windows_cmnt,	
				'power_trunk_cmnt'          => $request->power_trunk_cmnt,	
				'power_locks_cmnt'          => $request->power_locks_cmnt,	
				'power_mirrors_cmnt'        => $request->power_mirrors_cmnt,	
				'power_folding_mirrors_cmnt'=> $request->power_folding_mirrors_cmnt,	
				'sun_roof_cmnt'             => $request->sun_roof_cmnt,	
				'panoramic_roof_cmnt'       => $request->panoramic_roof_cmnt,	
				'cool_box_cmnt'             => $request->cool_box_cmnt,	
				'seat_heated_front_cmnt'    => $request->seat_heated_front_cmnt,	
				'auto_park_cmnt'            => $request->auto_park_cmnt,	
				'remote_start_engine_cmnt'  => $request->remote_start_engine_cmnt,	
				'soft_close_doors_cmnt'     => $request->soft_close_doors_cmnt,	
				'adaptive_lights_cmnt'      => $request->adaptive_lights_cmnt,	
				'night_vision_cmnt'         => $request->night_vision_cmnt,	
				'captain_rear_seats_cmnt'   => $request->captain_rear_seats_cmnt,	
				'leather_seats_cmnt'        => $request->leather_seats_cmnt,	
				'leather_fabric_cmnt'       => $request->leather_fabric_cmnt,	
				'body_kit_cmnt'             => $request->body_kit_cmnt,	
				'lift_kit_cmnt'             => $request->lift_kit_cmnt,	
				'front_spoiler_cmnt'        => $request->front_spoiler_cmnt,	
				'rear_spoiler_cmnt'         => $request->rear_spoiler_cmnt,	
				'fog_light_front_cmnt'      => $request->fog_light_front_cmnt,	
				'roof_carrier_cmnt'         => $request->roof_carrier_cmnt,	
				'halogen_headlight_cmnt'    => $request->halogen_headlight_cmnt,	
				'led_headlight_cmnt'        => $request->led_headlight_cmnt,	
				'xenon_headlight_cmnt'      => $request->xenon_headlight_cmnt,	
				'trailer_hook_coupling_cmnt'=> $request->trailer_hook_coupling_cmnt,
				
				);
         
		if($update_id == '' || $update_id == null)
		{
			//$insertid = DB::table('tbl_report_vehicle_spec')->insertGetId($data);
			$insertid_one = DB::table('tbl_report_spec_check_one')->insertGetId($dataOne);
			$insertid_two = DB::table('tbl_report_spec_check_two')->insertGetId($dataTwo);
			$insertid = $edit_id;
 		}
		else 
		{
			//$deleteid = DB::table('tbl_report_vehicle_spec')->where('vehicle_spec_report_id',$edit_id)->delete();
			//$data['vehicle_spec_editedby'] = Auth::user()->id;
			//$update = DB::table('tbl_report_vehicle_spec')
					//->where('vehicle_spec_report_id',$edit_id)
					//->insert($data); //->update($data);
	
			$deleteid_one = DB::table('tbl_report_spec_check_one')->where('one_report_id',$edit_id)->delete();
			$dataOne['one_editedby'] = Auth::user()->id;
			$updateOne = DB::table('tbl_report_spec_check_one')
					->where('one_report_id',$edit_id)
					->insert($dataOne);  
			
			$deleteid_two = DB::table('tbl_report_spec_check_two')->where('two_report_id',$edit_id)->delete();
			$dataTwo['two_editedby'] = Auth::user()->id;					
			$updateTwo = DB::table('tbl_report_spec_check_two')
					->where('two_report_id',$edit_id)
					->insert($dataTwo);  
			
			$insertid = $edit_id;
		}
   
		if($insertid)
		{
			return response()->json(['status' => 1, 'msg' => 'Vehicle Specification Added Successfully', 'heading' => 'Success','id'=>$insertid]);
		}
		else
		{
			return response()->json(['status' => 0, 'msg' => 'Error !try again', 'heading' => 'Error']);
		}
    }
	
	/********** Inspection Checklist  **********/
	public function add_inspectionChecklist(Request $request)
	{
        $edit_id   = $request->edit_id; /** report_id for insert other tables **/   
        $update_id = $request->update_id; /** report_id for edit **/    
		
		$data_one = array(
				'one_checklist_report_id' => $edit_id, 
				'one_checklist_status'    => 0, 
				'one_checklist_addedby'   => Auth::user()->id,
				'one_checklist_ip'        => $request->ip(),
				/**** Exterior ****/
				'door_locks_operation'    => $request->door_locks_operation,	
				'fuel_filler_cover_petrol'=> $request->fuel_filler_cover_petrol,	
				'glass'                   => $request->glass,	
				'molding'                 => $request->molding,	
				'bumper_grills'           => $request->bumper_grills,	 
				'front_bumper'            => $request->front_bumper,	 
				'rear_bumper'             => $request->rear_bumper,	
				'front_left_headlights'   => $request->front_left_headlights,	
				'front_right_headlights'  => $request->front_right_headlights,	
				'rear_left_tail_lights'   => $request->rear_left_tail_lights,	
				'rear_right_tail_lights'  => $request->rear_right_tail_lights,	
				'general_body_condition'  => $request->general_body_condition,

				/**** Interior ****/
				'seat_belts'            => $request->seat_belts,	
				'headliner'             => $request->headliner,	
				'rearview_mirror'       => $request->rearview_mirror,	
				'steering_wheel'        => $request->steering_wheel,	
				'gear_lever'            => $request->gear_lever,	
				'sun_visor'             => $request->sun_visor,	
				'pillar_trim'           => $request->pillar_trim,	
				'armrest_console'       => $request->armrest_console,
				'floor_mats_carpets'    => $request->floor_mats_carpets,
				'trunk_liner'           => $request->trunk_liner,
				'dashboard'             => $request->dashboard,
				'glove_compartment'     => $request->glove_compartment,
				'seats'                 => $request->seats,
				'door_trims'            => $request->door_trims,
				'ac_grills'             => $request->ac_grills,   
				'sunroof_shade_liner'   => $request->sunroof_shade_liner,

				/**** Tyre ****/
				'spare_tyre'            => $request->spare_tyre,	
				'front_left_tyre'       => $request->front_left_tyre,	
				'back_right_tyre'       => $request->back_right_tyre,	
				'front_right_tyre'      => $request->front_right_tyre,	
				'back_left_tyre'        => $request->back_left_tyre,	
				
				/**** Engine ****/
				'coolant_level'         => $request->coolant_level,	
				'coolant_leaks'         => $request->coolant_leaks,	
				'steering_fluid'        => $request->steering_fluid,	
				'brake_master_booster'  => $request->brake_master_booster,	
				'evidence_overheating'  => $request->evidence_overheating,	
				'coolant_conditions'    => $request->coolant_conditions,	
				'radiator_cap'          => $request->radiator_cap,	
				'radiator_fan'          => $request->radiator_fan,
				
				'fender_liner'          => $request->fender_liner,  
				'hoses_pipes'           => $request->hoses_pipes, 
				'cable_harnes_connector'=> $request->cable_harnes_connector,  
				'power_steer_fluidlevel'=> $request->power_steer_fluidlevel,  
				'engine_oil_level'      => $request->engine_oil_level, 
				'external_engine_leaks' => $request->external_engine_leaks, 
				'engine_mounts'         => $request->engine_mounts, 
				'turbo_supercharger'    => $request->turbo_supercharger, 
				'fuel_pump_pipes'       => $request->fuel_pump_pipes,
				
				'cold_starting'         => $request->cold_starting, 
				'fast_idle'             => $request->fast_idle,  
				'noise_level'           => $request->noise_level, 
				'excess_smoke'          => $request->excess_smoke, 
				'inlet_manifold'        => $request->inlet_manifold, 
				'outlet_manifold'       => $request->outlet_manifold, 
				'exhaust_pipes'         => $request->exhaust_pipes, 
				'silencer'              => $request->silencer, 
				'head_shield_mounting'  => $request->head_shield_mounting, 
				'joints_couplings'      => $request->joints_couplings, 
				'engine_underside_leak' => $request->engine_underside_leak,
				'catalytic_converter'   => $request->catalytic_converter,
				'engine_shield'         => $request->engine_shield,
				
				/**** Comments ****/
				/**** Exterior ****/
				'door_locks_operation_cmnt'    => $request->door_locks_operation_cmnt,	
				'fuel_filler_cover_petrol_cmnt'=> $request->fuel_filler_cover_petrol_cmnt,	
				'glass_cmnt'                   => $request->glass_cmnt,	
				'molding_cmnt'                 => $request->molding_cmnt,	
				'bumper_grills_cmnt'           => $request->bumper_grills_cmnt,	 
				'front_bumper_cmnt'            => $request->front_bumper_cmnt,	 
				'rear_bumper_cmnt'             => $request->rear_bumper_cmnt,	
				'front_left_headlights_cmnt'   => $request->front_left_headlights_cmnt,	
				'front_right_headlights_cmnt'  => $request->front_right_headlights_cmnt,	
				'rear_left_tail_lights_cmnt'   => $request->rear_left_tail_lights_cmnt,	
				'rear_right_tail_lights_cmnt'  => $request->rear_right_tail_lights_cmnt,	
				'general_body_condition_cmnt'  => $request->general_body_condition_cmnt,

				/**** Interior ****/
				'seat_belts_cmnt'            => $request->seat_belts_cmnt,	
				'headliner_cmnt'             => $request->headliner_cmnt,	
				'rearview_mirror_cmnt'       => $request->rearview_mirror_cmnt,	
				'steering_wheel_cmnt'        => $request->steering_wheel_cmnt,	
				'gear_lever_cmnt'            => $request->gear_lever_cmnt,	
				'sun_visor_cmnt'             => $request->sun_visor_cmnt,	
				'pillar_trim_cmnt'           => $request->pillar_trim_cmnt,	
				'armrest_console_cmnt'       => $request->armrest_console_cmnt,
				'floor_mats_carpets_cmnt'    => $request->floor_mats_carpets_cmnt,
				'trunk_liner_cmnt'           => $request->trunk_liner_cmnt,
				'dashboard_cmnt'             => $request->dashboard_cmnt,
				'glove_compartment_cmnt'     => $request->glove_compartment_cmnt,
				'seats_cmnt'                 => $request->seats_cmnt,
				'door_trims_cmnt'            => $request->door_trims_cmnt,
				'ac_grills_cmnt'             => $request->ac_grills_cmnt,   
				'sunroof_shade_liner_cmnt'   => $request->sunroof_shade_liner_cmnt,

				/**** Tyre ****/
				'spare_tyre_cmnt'            => $request->spare_tyre_cmnt,	
				'front_left_tyre_cmnt'       => $request->front_left_tyre_cmnt,	
				'back_right_tyre_cmnt'       => $request->back_right_tyre_cmnt,	
				'front_right_tyre_cmnt'      => $request->front_right_tyre_cmnt,	
				'back_left_tyre_cmnt'        => $request->back_left_tyre_cmnt,	
				
				/**** Engine ****/
				'coolant_level_cmnt'         => $request->coolant_level_cmnt,	
				'coolant_leaks_cmnt'         => $request->coolant_leaks_cmnt,	
				'steering_fluid_cmnt'        => $request->steering_fluid_cmnt,	
				'brake_master_booster_cmnt'  => $request->brake_master_booster_cmnt,	
				'evidence_overheating_cmnt'  => $request->evidence_overheating_cmnt,	
				'coolant_conditions_cmnt'    => $request->coolant_conditions_cmnt,	
				'radiator_cap_cmnt'          => $request->radiator_cap_cmnt,	
				'radiator_fan_cmnt'          => $request->radiator_fan_cmnt,
				
				'fender_liner_cmnt'          => $request->fender_liner_cmnt,  
				'hoses_pipes_cmnt'           => $request->hoses_pipes_cmnt, 
				'cable_harnes_connector_cmnt'=> $request->cable_harnes_connector_cmnt,  
				'power_steer_fluidlevel_cmnt'=> $request->power_steer_fluidlevel_cmnt,  
				'engine_oil_level_cmnt'      => $request->engine_oil_level_cmnt, 
				'external_engine_leaks_cmnt' => $request->external_engine_leaks_cmnt, 
				'engine_mounts_cmnt'         => $request->engine_mounts_cmnt, 
				'turbo_supercharger_cmnt'    => $request->turbo_supercharger_cmnt, 
				'fuel_pump_pipes_cmnt'       => $request->fuel_pump_pipes_cmnt,
				
				'cold_starting_cmnt'         => $request->cold_starting_cmnt, 
				'fast_idle_cmnt'             => $request->fast_idle_cmnt,  
				'noise_level_cmnt'           => $request->noise_level_cmnt, 
				'excess_smoke_cmnt'          => $request->excess_smoke_cmnt, 
				'inlet_manifold_cmnt'        => $request->inlet_manifold_cmnt, 
				'outlet_manifold_cmnt'       => $request->outlet_manifold_cmnt, 
				'exhaust_pipes_cmnt'         => $request->exhaust_pipes_cmnt, 
				'silencer_cmnt'              => $request->silencer_cmnt, 
				'head_shield_mounting_cmnt'  => $request->head_shield_mounting_cmnt, 
				'joints_couplings_cmnt'      => $request->joints_couplings_cmnt, 
				'engine_underside_leak_cmnt' => $request->engine_underside_leak_cmnt,
				'catalytic_converter_cmnt'   => $request->catalytic_converter_cmnt,
				'engine_shield_cmnt'         => $request->engine_shield_cmnt,
				);
				
		$data_two = array(
				'two_checklist_report_id'=> $edit_id, 
				'two_checklist_status'   => 0, 
				'two_checklist_addedby'  => Auth::user()->id,
				'two_checklist_ip'       => $request->ip(),
				/**** Transmission ****/
				'gear_selector'         => $request->gear_selector,	
				'gear_shifting'         => $request->gear_shifting,	
				'transmission_mount'    => $request->transmission_mount,	
				'gear_noise'            => $request->gear_noise,	
				'fluid_level_oil_leak'  => $request->fluid_level_oil_leak,
				/**** Electrical ****/
				'door_locks'            => $request->door_locks,	
				'central_locking'       => $request->central_locking,	
				'ignitionlock_startsys' => $request->ignitionlock_startsys,	
				'instrument_panel'      => $request->instrument_panel,	
				'headlights'            => $request->headlights,	
				'sidelights_runlights'  => $request->sidelights_runlights,	
				'rear_lights'           => $request->rear_lights,	
				'indicator_hazardlights'=> $request->indicator_hazardlights,	
				'boot_tailgate_lock'    => $request->boot_tailgate_lock,	
				'reverse_lights'        => $request->reverse_lights,	
				'fog_lights'            => $request->fog_lights,	
				'multimedia'            => $request->multimedia,	
				'ac_control_cooling'    => $request->ac_control_cooling,	
				'side_mirror'           => $request->side_mirror,
				'auxiliary_lights'      => $request->auxiliary_lights,
				'panel_lights'          => $request->panel_lights,
				'horn'                  => $request->horn,
				'window_operation'      => $request->window_operation,
				'sunroof_operation'     => $request->sunroof_operation,
				'wipers_jet_washers'    => $request->wipers_jet_washers,
				'keys_remote_controls'  => $request->keys_remote_controls,
				'warning_lights'        => $request->warning_lights,
				'number_plate_light'    => $request->number_plate_light,
				/**** Underbody ****/
				'steering_ball_joints'  => $request->steering_ball_joints,	
				'brakes_lines'          => $request->brakes_lines,	
				'subframe'              => $request->subframe,	
				'power_steering_rack'   => $request->power_steering_rack,	
				'wheels_hubs_bearings'  => $request->wheels_hubs_bearings,	
				'dampers_bushes'        => $request->dampers_bushes,	
				'evidencefloor_chassis' => $request->evidencefloor_chassis,
				/**** Test Drive ****/
				'engine_performance'    => $request->engine_performance,	
				'gearbox_operation'     => $request->gearbox_operation,	
				'clutch_operation'      => $request->clutch_operation,	
				'steering_operation'    => $request->steering_operation,	
				'brake_operation'       => $request->brake_operation,	
				'hand_parking_brake'    => $request->hand_parking_brake,	
				'drive_train'           => $request->drive_train,	
				'instru_control_func'   => $request->instru_control_func,	
				'suspension_noise'      => $request->suspension_noise,	 
				'shock_absorber'        => $request->shock_absorber,	 
				'road_holding_stability'=> $request->road_holding_stability,	 
				'nois'                  => $request->nois,	
				
				/**** Comments ****/
				/**** Transmission ****/
				'gear_selector_cmnt'         => $request->gear_selector_cmnt,	
				'gear_shifting_cmnt'         => $request->gear_shifting_cmnt,	
				'transmission_mount_cmnt'    => $request->transmission_mount_cmnt,	
				'gear_noise_cmnt'            => $request->gear_noise_cmnt,	
				'fluid_level_oil_leak_cmnt'  => $request->fluid_level_oil_leak_cmnt,
				/**** Electrical ****/
				'door_locks_cmnt'            => $request->door_locks_cmnt,	
				'central_locking_cmnt'       => $request->central_locking_cmnt,	
				'ignitionlock_startsys_cmnt' => $request->ignitionlock_startsys_cmnt,	
				'instrument_panel_cmnt'      => $request->instrument_panel_cmnt,	
				'headlights_cmnt'            => $request->headlights_cmnt,	
				'sidelights_runlights_cmnt'  => $request->sidelights_runlights_cmnt,	
				'rear_lights_cmnt'           => $request->rear_lights_cmnt,	
				'indicator_hazardlights_cmnt'=> $request->indicator_hazardlights_cmnt,	
				'boot_tailgate_lock_cmnt'    => $request->boot_tailgate_lock_cmnt,	
				'reverse_lights_cmnt'        => $request->reverse_lights_cmnt,	
				'fog_lights_cmnt'            => $request->fog_lights_cmnt,	
				'multimedia_cmnt'            => $request->multimedia_cmnt,	
				'ac_control_cooling_cmnt'    => $request->ac_control_cooling_cmnt,	
				'side_mirror_cmnt'           => $request->side_mirror_cmnt,
				'auxiliary_lights_cmnt'      => $request->auxiliary_lights_cmnt,
				'panel_lights_cmnt'          => $request->panel_lights_cmnt,
				'horn_cmnt'                  => $request->horn_cmnt,
				'window_operation_cmnt'      => $request->window_operation_cmnt,
				'sunroof_operation_cmnt'     => $request->sunroof_operation_cmnt,
				'wipers_jet_washers_cmnt'    => $request->wipers_jet_washers_cmnt,
				'keys_remote_controls_cmnt'  => $request->keys_remote_controls_cmnt,
				'warning_lights_cmnt'        => $request->warning_lights_cmnt,
				'number_plate_light_cmnt'    => $request->number_plate_light_cmnt,
				/**** Underbody ****/
				'steering_ball_joints_cmnt'  => $request->steering_ball_joints_cmnt,	
				'brakes_lines_cmnt'          => $request->brakes_lines_cmnt,	
				'subframe_cmnt'              => $request->subframe_cmnt,	
				'power_steering_rack_cmnt'   => $request->power_steering_rack_cmnt,	
				'wheels_hubs_bearings_cmnt'  => $request->wheels_hubs_bearings_cmnt,	
				'dampers_bushes_cmnt'        => $request->dampers_bushes_cmnt,	
				'evidencefloor_chassis_cmnt' => $request->evidencefloor_chassis_cmnt,
				/**** Test Drive ****/
				'engine_performance_cmnt'    => $request->engine_performance_cmnt,	
				'gearbox_operation_cmnt'     => $request->gearbox_operation_cmnt,	
				'clutch_operation_cmnt'      => $request->clutch_operation_cmnt,	
				'steering_operation_cmnt'    => $request->steering_operation_cmnt,	
				'brake_operation_cmnt'       => $request->brake_operation_cmnt,	
				'hand_parking_brake_cmnt'    => $request->hand_parking_brake_cmnt,	
				'drive_train_cmnt'           => $request->drive_train_cmnt,	
				'instru_control_func_cmnt'   => $request->instru_control_func_cmnt,	
				'suspension_noise_cmnt'      => $request->suspension_noise_cmnt,	 
				'shock_absorber_cmnt'        => $request->shock_absorber_cmnt,	 
				'road_holding_stability_cmnt'=> $request->road_holding_stability_cmnt,	 
				'nois_cmnt'                  => $request->nois_cmnt,
				);
 
		if($update_id == '' || $update_id == null)
		{
			//$insertid = DB::table('tbl_report_insp_checklist')->insertGetId($data);
			$insertid_one = DB::table('tbl_report_insp_check_one')->insertGetId($data_one);
			$insertid_two = DB::table('tbl_report_insp_check_two')->insertGetId($data_two);
			$insertid = $edit_id;
 		}
		else 
		{
			//$deleteid = DB::table('tbl_report_insp_checklist')->where('insp_checklist_report_id',$edit_id)->delete();
			//$data['insp_checklist_editedby'] = Auth::user()->id;
			//$update = DB::table('tbl_report_insp_checklist')
					//->where('insp_checklist_report_id',$edit_id)
					//->insert($data); //->update($data);
					
			$deleteid_one = DB::table('tbl_report_insp_check_one')->where('one_checklist_report_id',$edit_id)->delete();
			$data_one['one_checklist_editedby'] = Auth::user()->id;
			$update_one = DB::table('tbl_report_insp_check_one')
					->where('one_checklist_report_id',$edit_id)
					->insert($data_one); //->update($data);
					
			$deleteid_two = DB::table('tbl_report_insp_check_two')->where('two_checklist_report_id',$edit_id)->delete();
			$data_two['two_checklist_editedby'] = Auth::user()->id;
			$update_one = DB::table('tbl_report_insp_check_two')
					->where('two_checklist_report_id',$edit_id)
					->insert($data_two); //->update($data);
			
			$insertid = $edit_id;
		}

		if($insertid)
		{
			return response()->json(['status' => 1, 'msg' => 'Inspection Checklist Added Successfully', 'heading' => 'Success','id'=>$insertid]);
		}
		else
		{
			return response()->json(['status' => 0, 'msg' => 'Error !try again', 'heading' => 'Error']);
		}
    }
	
	/********** Gallery **********/
	public function add_inspectionGallery(Request $request)
    {
        $edit_id   = $request->edit_id; /** report_id for insert other tables **/   
        $update_id = $request->update_id; /** report_id for edit **/    
		
		/***** Video *****/ 
		//dd($request->hasFile('video_file'));
		
		if($request->hasFile('video_file')!='')
		{
			$cv = $request->video_file;   
			$file_name = str::random(30) . '.' . $cv->getClientOriginalExtension();
			$imageFileType1 = $cv->getClientOriginalExtension();
			/*if($imageFileType1 != 'jpg' && $imageFileType1 != 'png' && $imageFileType1 != 'jpeg')
			{
				return response()->json(['status'=>0,'msg'=>'Please select a valid File!','heading'=> 'Success']);
			}*/
			$path = base_path() . '/public/uploads/inspectionreport/videos';
			$cv->move($path, $file_name);
			//$dataVideo['video_file'] = $file_name;
		}
		else
		{
			$file_name = '';
		}    
		
			$dataVideo = array(
					'video_report_id' => $edit_id, 
					'video_status'    => 0, 
					'video_addedby'   => Auth::user()->id,
					'video_ip'        => $request->ip(),
					'video_url'       => $request->video_url,
					'video_file'      => $file_name,
				);
		 	
		/***** Video *****/
		 
		if($edit_id == '')
		{
			$insertVideoid = DB::table('tbl_report_video')->insertGetId($dataVideo);
			
			/******* GALLERY INSERT START *******/
			$filename = $request->file;
			
			if($request->file!='')
			{  
				$tab_name  = $request->file;
				$count_int = count($tab_name);
		   
				if($count_int>0)
				{
					for ($i=0; $i<$count_int ; $i++) 
					{ 
						if($request->file('file')!='')
						{
							$cv = $request->file[$i];
							$file_name = str::random(30) . '.' . $cv->getClientOriginalExtension();
							$imageFileType1 = $cv->getClientOriginalExtension();
							if($imageFileType1 != 'jpg' && $imageFileType1 != 'png' && $imageFileType1 != 'jpeg')
							{
								return response()->json(['status'=>0,'msg'=>'Please select a valid File!','heading'=> 'Success']);
							}
							$filesize = $request->file[$i]->getSize();
							if($filesize > 2000000)
							{
								return response()->json(['status'=>0,'msg'=>'Please select a Image size less than 2 MB!', 'heading'=>'Success']);
							} 
							 
							$path = base_path() . '/public/uploads/inspectionreport/gallery';
							$cv->move($path, $file_name);
						}
					
						$insertid = DB::table('tbl_report_gallery')->insert([
									'gallery_report_id' => $edit_id,
									'gallery_status'  => '0',
									'gallery_addedby' => Auth::user()->id,
									'gallery_ip'      => $request->ip(),
									'gallery_image'   => $file_name,
									'gallery_image_type' => $request->gallery_image_type[$i],
									'gallery_image_desc' => $request->gallery_image_desc[$i],
								]);
					}
				}
			}  
			/******* GALLERY INSERT END *******/
			$insertid = $edit_id;
 		}
		else 
		{
			$dataVideoid = DB::table('tbl_report_video')->where('video_report_id',$edit_id)->delete();
			$dataVideo['video_editedby'] = Auth::user()->id;
			$update = DB::table('tbl_report_video')
					->where('video_report_id',$edit_id)
					->insert($dataVideo);
	
			/******* GALLERY UPDATE START *******/
			$delete_img = DB::table('tbl_report_gallery')
					->where('gallery_report_id', $edit_id)
					->update(['gallery_status'   => 1,
							  'gallery_editedby' => Auth::user()->id]);
			
			if($request->file != null )
			{ 
				$tabname   = $request->file;
				$count_int = count($tabname);
	   
				for ($i=0; $i<$count_int ; $i++) 
				{
					$inData = array(
							'gallery_ip'        => $request->ip(),
							'gallery_addedby'   => Auth::user()->id,
							'gallery_status'    => '0',
							'gallery_report_id' => $edit_id,
							//'gallery_image_type'=> $request->gallery_image_type[$i],
							//'gallery_image_desc'=> $request->gallery_image_desc[$i],
						);
 
					if(!empty($request->file[$i]))
					{
						$cv = $request->file[$i];
						$file_name = str::random(30) . '.' . $cv->getClientOriginalExtension();
						$imageFileType1 = $cv->getClientOriginalExtension();
						if ($imageFileType1 != 'jpg' && $imageFileType1 != 'png' && $imageFileType1 != 'jpeg')
						{
							return response()->json(['status'=>0,'msg' =>'Please select a valid File!','heading'=>'Success']);
						}
						  
						$filesize = $request->file[$i]->getSize();
						  
						if($filesize > 2000000)
						{
							return response()->json(['status'=>0,'msg'=>'Please select a Image size less than 2 MB!','heading' => 'Success']);
						} 
						 
						$path = base_path() . '/public/uploads/inspectionreport/gallery';
						$cv->move($path, $file_name);
						
						$data['gallery_image']   = $file_name;
						$inData['gallery_image'] = $file_name;
						
						$data['gallery_image_type']   = $request->gallery_image_type[$i];  
						$inData['gallery_image_type'] = $request->gallery_image_type[$i];
						
						$data['gallery_image_desc']   = $request->gallery_image_desc[$i];
						$inData['gallery_image_desc'] = $request->gallery_image_desc[$i];
			
						$club_names = DB::table('tbl_report_gallery')->insert([$inData]);
					}
				}
			}	 
			if( $request->docimg != '')
			{
				for ($i=0; $i<count($request->docimg); $i++) 
				{
					$d1 = $request->docimg[$i];  
					$previousData = DB::table('tbl_report_gallery')
							->where('gallery_id',$d1)
							->first();   
			
					if($previousData)  
					{
						$inData1 = array(
								'gallery_ip'        => $request->ip(),
								'gallery_addedby'   => Auth::user()->id,
								'gallery_status'    => '0',
								'gallery_report_id' => $edit_id,
								'gallery_image'     => $previousData->gallery_image,
								'gallery_image_type'=> $previousData->gallery_image_type, //$request->gallery_image_type[$i],
								'gallery_image_desc'=> $previousData->gallery_image_desc, //$request->gallery_image_desc[$i],
							);    
							
						$club_names1 = DB::table('tbl_report_gallery')->insert([$inData1]);   
					}
				}            
			}
			/******* GALLERY UPDATE END *******/
	
			/******* GALLERY UPDATE START *******/
			$insertid = $edit_id;
		}

		if($insertid)
		{
			return response()->json(['status'=>1,'msg'=>'Inspection Gallery Added Successfully','heading'=>'Success','id'=>$insertid]);
		}
		else
		{
			return response()->json(['status' => 0, 'msg' => 'Error !try again', 'heading' => 'Error']);
		}
    }
	
	/********** GALLERY END **********/
	
	/********** REPORTS START **********/
	public function addReportsfile(Request $request)
    {
        $edit_id   = $request->edit_id; /** report_id for insert other tables **/   
        $update_id = $request->update_id; /** report_id for edit **/    

		if($edit_id == '')
		{
			/******* REPORTS INSERT START *******/
			$filename = $request->report_file;
			if($request->report_file!='')
			{
				$tab_name  = $request->report_file;
				$count_int = count($tab_name);
		   
				if($count_int>0)
				{
					for ($i=0; $i<$count_int ; $i++) 
					{ 
						if($request->file('report_file')!='')
						{
							$cv = $request->file[$i];
							$file_name = str::random(30) . '.' . $cv->getClientOriginalExtension();
							$imageFileType1 = $cv->getClientOriginalExtension();
							if($imageFileType1 != 'pdf' && $imageFileType1 != 'docx')
							{
								return response()->json(['status'=>0,'msg'=>'Please select a valid File!','heading'=> 'Success']);
							}
							$filesize = $request->file[$i]->getSize();
							if($filesize > 2000000)
							{
								return response()->json(['status'=>0,'msg'=>'Please select a Image size less than 1 MB!', 'heading'=>'Success']);
							} 
							 
							$path = base_path() . '/public/uploads/inspectionreport/reports';
							$cv->move($path, $file_name);
						}
					
						$insertid = DB::table('tbl_report_reports')->insert([
									'rep_report_id' => $edit_id,
									'rep_status'  => '0',
									'rep_addedby' => Auth::user()->id,
									'rep_ip'      => $request->ip(),
									'rep_file'    => $file_name,
								]);
					}
				}
			}  
			/******* REPORTS INSERT END *******/
			$insertid = $edit_id;
 		}
		else 
		{
			/******* REPORTS UPDATE START *******/
			$delete_img = DB::table('tbl_report_reports')
					->where('rep_report_id', $edit_id)
					->update(['rep_status'   => 1,
							  'rep_editedby' => Auth::user()->id]);
			
			if($request->report_file != null )
			{
				$tabname   = $request->report_file;
				$count_int = count($tabname);
	   
				for ($i=0; $i<$count_int ; $i++) 
				{
					$inData = array(
							'rep_ip'        => $request->ip(),
							'rep_addedby'   => Auth::user()->id,
							'rep_status'    => '0',
							'rep_report_id' => $edit_id,
						);
						
					if(!empty($request->report_file[$i]))
					{
						$cv = $request->report_file[$i];
						$file_name = str::random(30) . '.' . $cv->getClientOriginalExtension();
						$imageFileType1 = $cv->getClientOriginalExtension();
						if ($imageFileType1 != 'pdf' && $imageFileType1 != 'docx')
						{
							return response()->json(['status'=>0,'msg' =>'Please select a valid File!','heading'=>'Success']);
						}
						  
						$filesize = $request->report_file[$i]->getSize();
						  
						if($filesize > 2000000)
						{
							return response()->json(['status'=>0,'msg'=>'Please select a Image size less than 1 MB!','heading' => 'Success']);
						} 
						 
						$path = base_path() . '/public/uploads/inspectionreport/reports';
						$cv->move($path, $file_name);
						
						$data['rep_file']   = $file_name;
						$inData['rep_file'] = $file_name;
						
						$club_names = DB::table('tbl_report_reports')->insert([$inData]);
					}
				}
			}	 
			if( $request->docfile != '')
			{
				for ($i=0; $i<count($request->docfile); $i++) 
				{
					$d1 = $request->docfile[$i];
					$previousData = DB::table('tbl_report_reports')
							->where('rep_id',$d1)
							->first();
 	
					if($previousData)  
					{
						$inData1 = array(
								'rep_ip'        => $request->ip(),
								'rep_addedby'   => Auth::user()->id,
								'rep_status'    => '0',
								'rep_report_id' => $edit_id,
								'rep_file'      => $previousData->rep_file
							);    
						 $club_names1 = DB::table('tbl_report_reports')->insert([$inData1]);   
					 }
				}            
			}
			/******* REPORTS UPDATE END *******/
			$insertid = $edit_id;
		}
             
		if($insertid)
		{
			return response()->json(['status'=>1,'msg'=>'Inspection Reports Added Successfully','heading'=>'Success','id'=>$insertid]);
		}
		else
		{
			return response()->json(['status' => 0, 'msg' => 'Error !try again', 'heading' => 'Error']);
		}
    }
	
	/********** REPORTS END **********/
	
	/********** DAMAGES START **********/
	public function addDamages(Request $request)
    {
		$edit_id   = $request->editid;   //dd(damage_report_id);/** report_id for insert other tables **/   
        $update_id = $request->update_id; /** report_id for edit **/    
		
		/**** Image Start ****/
		$pngUrl = $request->pngUrl;
		if(preg_match('/data:image\/(gif|jpeg|png);base64,(.*)/i', $pngUrl, $matches))
		{
			$imageType = $matches[1];
			$imageData = base64_decode($matches[2]);

			$image = imagecreatefromstring($imageData);
			$filename = md5($imageData) . '.png';   // dd($filename);
			
			if(imagepng($image, base_path() . '/public/uploads/inspectionreport/damages/' . $filename))
			{
				//echo json_encode(array('filename' => base_path() . '/public/uploads/inspectionreport/damages/' . $filename));
			} 
			else 
			{
				throw new Exception('Could not save the file.');
			}
		
			$data = array(
					'damage_report_id' => $edit_id, 
					'damage_ip'        => $request->ip(),
					'damage_addedby'   => Auth::user()->id,
					'damage_status'    => 0, 
					'damage_image'     => $filename, 				
					);
			 
			if($update_id == '' || $update_id == null)
			{  
				$insertid = DB::table('tbl_report_damages')->insertGetId($data);
				$insertid = $edit_id;
			}
			else 
			{  
				$delete_img = DB::table('tbl_report_damages')
					->where('damage_report_id', $edit_id)
					->update(['damage_status'   => 1,
							  'damage_editedby' => Auth::user()->id]);
			
				$data['damage_addedby'] = Auth::user()->id;
				$update = DB::table('tbl_report_damages')
					->where('damage_id',$edit_id)
					->insert($data); //->update($data);
				
				$insertid = $edit_id;
			}
		
			if($insertid)
			{
				return response()->json(['status'=> 1, 'msg' => 'Damages Added Successfully', 'heading' => 'Success','id'=>$insertid]);
			}
			else
			{
				return response()->json(['status' => 0, 'msg' => 'Error !try again', 'heading' => 'Error']);
			}
		}
    }
	/********** DAMAGES END **********/
	
	/****************  EDIT START  ****************/
	public function getDivisionAction(Request $request)
    {
        ini_set('memory_limit', '-1');    
        
        $divisionId = $request->report_id;    
        
        $report = DB::table('tbl_report')
			->select('report_id','report_reference_no','report_client_name','report_client_name_ar','report_date_of_inspection')
			->where('report_id', $divisionId)
			->where('report_status', 0)
			->first();
 	
		$vehicle = DB::table('tbl_report_vehicle_info')
            ->select('vehicle_info_id','vehicle_info_report_id','vehicle_info_title','vehicle_info_model_year','vehicle_info_manuf_year','vehicle_info_chassis_no','vehicle_info_odometer','vehicle_info_condition')
            ->where('vehicle_info_report_id',$divisionId)
            ->where('vehicle_info_status',0)
            ->first();	 
		
		$addspec = DB::table('tbl_report_additionl_spec')
            ->select('add_spec_id','add_spec_report_id','add_spec_region','add_spec_exterior_color', 'add_spec_interior_color','add_spec_gearbox','add_spec_fuel_type','add_spec_steering_side', 'add_spec_cylinders','add_spec_engine_size','add_spec_keys','add_spec_doors','add_spec_seats')
            ->where('add_spec_report_id',$divisionId)
            ->where('add_spec_status',0)
            ->first();
 
		$warranty = DB::table('tbl_report_warranty_service')
            ->select('war_service_id','war_service_history','war_service_last','war_service_next','war_service_report_id')
            ->where('war_service_report_id',$divisionId)
            ->where('war_service_status',0)
            ->first();
		
		$overview = DB::table('tbl_report_overview')
            ->select('overview_id', 'overview_report_id', 'overview_english', 'overview_arabic')
            ->where('overview_report_id',$divisionId)
            ->where('overview_status',0)
            ->first();
			
		$summary = DB::table('tbl_report_insp_summary')
			->select('insp_summary_report_id','insp_summary_type','insp_summary_desc_id','insp_summary_desc','insp_summary_desc_ar')
            ->where('insp_summary_report_id',$divisionId)
            ->where('insp_summary_status',0)
            ->get();

		$summary_type = DB::table('tbl_summary_type')
            ->select('summary_type_id','summary_type_name')
			->where('summary_type_status',0)
            ->get(); 

		$summary_desc = DB::table('tbl_summary_description')
            ->select('sum_desc_id','sum_desc_type','sum_desc_name','sum_desc_name_ar')
			->where('sum_desc_status',0)
            ->get();

		$vehiclespec = DB::table('tbl_report_vehicle_spec')
            ->select('vehicle_spec_report_id','hill_start_assist','launch_control','child_safety_seats', 'rear_parking_sensors','anti_lock_brakes','ebd','alarm','front_airbags','side_airbags','traction_control_sys', 'anti_glare_rear_view','tire_pressure_monitor','cd_player','mp_player','aux_audio_system','winch','body_kit', 'lift_kit','leather_seats','rear_seat_enter_sys','parking_sensors','rear_view_camera','navigation', 'fire_extinguisher')
            ->where('vehicle_spec_report_id',$divisionId)
            ->where('vehicle_spec_status',0)
            ->first();

		$checklist = DB::table('tbl_report_insp_checklist') 
            ->select('insp_checklist_id','glass','door_locks_operation','fuel_filler_cover_petrol', 'general_body_condition','seat_belts','headliner','rearview_mirror','steering_wheel','gear_lever','sun_visor', 'pillar_trim','armrest_console','spare_tyre','front_left_tyre','back_right_tyre','front_right_tyre', 'back_left_tyre','coolant_level','coolant_leaks','steering_fluid','brake_master_booster', 'evidence_overheating','coolant_conditions','radiator_cap','radiator_fan','gear_selector','gear_shifting', 'transmission_mount','gear_noise','fluid_level_oil_leak','door_locks','central_locking', 'ignitionlock_startingsys','instrument_panel','headlights','sidelights_runlights','rear_lights', 'indicator_hazardlights','boot_tailgate_lock','reverse_lights','fog_lights','multimedia','ac_control_cooling', 'steering_ball_joints','brakes_lines','subframe','power_steering_rack','wheels_hubs_bearings','dampers_bushes','evidencefloor_chassis','engine_performance','gearbox_operation','clutch_operation','steering_operation', 'brake_operation','hand_parking_brake','drive_train','instru_control_func')
            ->where('insp_checklist_report_id',$divisionId)
            ->where('insp_checklist_status',0)
            ->first();
         
        $gallery = DB::table('tbl_report_gallery')
				->select('gallery_id','gallery_report_id','gallery_image','gallery_image_type','gallery_image_desc')
				->where('gallery_report_id',$divisionId)
				->where('gallery_status',0)
				->get();
	
		$gallery_type = DB::table('tbl_gallery_type')
				->select('gallery_type_id','gallery_type_name')
				->where('gallery_type_status',0)
				->get(); 
	 
		$video = DB::table('tbl_report_video')
                ->select('video_id','video_report_id','video_url','video_file')
                ->where('video_report_id',$divisionId)
                ->where('video_status',0)
                ->first();   
 
		$repfile = DB::table('tbl_report_reports')
                ->select('rep_id','rep_report_id','rep_file')
                ->where('rep_report_id',$divisionId)
                ->where('rep_status',0)
                ->get();
       
        $damageImg = DB::table('tbl_report_damages')
                ->select('damage_id','damage_report_id','damage_image')
                ->where('damage_report_id',$divisionId)
                ->where('damage_status',0)
                ->orderBy('damage_id','desc')
                ->first(); 
	 
		$dataOne = DB::table('tbl_report_spec_check_one')
			->select('one_id','one_status','one_report_id','air_suspension','adaptive_air_suspension','differential_lock','paddle_shifters','tiptronic','hill_descent_assist','hill_start_assist','auto_hold','comfort_seats','sport_seats','sport_brakes','sport_suspension','sport_exhaust','lane_change','launch_control','child_safety_seats','front_view_camera','rear_view_camera','degree_camera','front_parking_sensors','rear_parking_sensors','lane_departure','anti_lock_brakes','ebd','alarm','front_airbags','side_airbags','traction_control_sys','park_assist','blind_spot_monitor','tire_pressure_monitor','anti_glare_rear_view','winch','body_kit_aaa','lift_kit_aaa','leather_seats_aaa','rear_seat_enter_sys_aaa','parking_sensors','rear_view_camera_aaa','navigation_aaa','fire_extinguisher','air_suspension_cmnt', 'adaptive_air_suspension_cmnt','differential_lock_cmnt','paddle_shifters_cmnt','tiptronic_cmnt', 'hill_descent_assist_cmnt','hill_start_assist_cmnt','auto_hold_cmnt','comfort_seats_cmnt','sport_seats_cmnt', 'sport_brakes_cmnt','sport_suspension_cmnt','sport_exhaust_cmnt','lane_change_cmnt','launch_control_cmnt', 'child_safety_seats_cmnt','front_view_camera_cmnt','rear_view_camera_cmnt','degree_camera_cmnt', 'front_parking_sensors_cmnt','rear_parking_sensors_cmnt','lane_departure_cmnt','anti_lock_brakes_cmnt', 'ebd_cmnt','alarm_cmnt','front_airbags_cmnt','side_airbags_cmnt','traction_control_sys_cmnt','park_assist_cmnt','blind_spot_monitor_cmnt','tire_pressure_monitor_cmnt','anti_glare_rear_view_cmnt','winch_cmnt','body_kit_aaa_cmnt','lift_kit_aaa_cmnt','leather_seats_aaa_cmnt','rear_seat_enter_sys_aaa_cmnt', 'parking_sensors_cmnt','rear_view_camera_aaa_cmnt','navigation_aaa_cmnt','fire_extinguisher_cmnt')
			->where('one_report_id',$divisionId)
			->where('one_status',0)
			->first();
			
		$dataTwo = DB::table('tbl_report_spec_check_two')
			->select('two_id','two_status','two_report_id','digital_driver_display','cd_player','dvd_player','mp_player', 'sd_card_player','bluetooth_interface','premium_sound_system','aux_audio_system','usb','usb_c','touch_screen', 'rear_seat_enter_sys','wireless','ambient_lighting','apple_carplay','navigation','standard_ac','dual_climcont_ac','multi_climcont_ac','keyless_entry','keyless_start','power_steering','heads_up_display','cruise_control','adaptive_cruise_control','seat_cooling_front','seat_cooling_rear','seat_massage_front','seat_massage_rear','driver_memory_seat','passenger_memory_seat','power_driver_seats','power_passenger_seats','power_rear_seats','power_front_windows','power_rear_windows','power_trunk','power_locks','power_mirrors','power_folding_mirrors','sun_roof','panoramic_roof','cool_box','seat_heated_front','auto_park','remote_start_engine','soft_close_doors','adaptive_lights','night_vision','captain_rear_seats','leather_seats','leather_fabric','body_kit','lift_kit','front_spoiler','rear_spoiler','fog_light_front','roof_carrier','halogen_headlight','led_headlight','xenon_headlight','trailer_hook_coupling','digital_driver_display_cmnt', 'cd_player_cmnt','dvd_player_cmnt','mp_player_cmnt','sd_card_player_cmnt','bluetooth_interface_cmnt', 'premium_sound_system_cmnt','aux_audio_system_cmnt','usb_cmnt','usb_c_cmnt','touch_screen_cmnt', 'rear_seat_enter_sys_cmnt','wireless_cmnt','ambient_lighting_cmnt','apple_carplay_cmnt','navigation_cmnt', 'standard_ac_cmnt','dual_climcont_ac_cmnt','multi_climcont_ac_cmnt','keyless_entry_cmnt','keyless_start_cmnt','power_steering_cmnt','heads_up_display_cmnt','cruise_control_cmnt','adaptive_cruise_control_cmnt', 'seat_cooling_front_cmnt','seat_cooling_rear_cmnt','seat_massage_front_cmnt','seat_massage_rear_cmnt', 'driver_memory_seat_cmnt','passenger_memory_seat_cmnt','power_driver_seats_cmnt','power_passenger_seats_cmnt', 'power_rear_seats_cmnt','power_front_windows_cmnt','power_rear_windows_cmnt','power_trunk_cmnt', 'power_locks_cmnt','power_mirrors_cmnt','power_folding_mirrors_cmnt','sun_roof_cmnt','panoramic_roof_cmnt', 'cool_box_cmnt','seat_heated_front_cmnt','auto_park_cmnt','remote_start_engine_cmnt','soft_close_doors_cmnt', 'adaptive_lights_cmnt','night_vision_cmnt','captain_rear_seats_cmnt','leather_seats_cmnt','leather_fabric_cmnt','body_kit_cmnt','lift_kit_cmnt','front_spoiler_cmnt','rear_spoiler_cmnt','fog_light_front_cmnt', 'roof_carrier_cmnt','halogen_headlight_cmnt','led_headlight_cmnt','xenon_headlight_cmnt', 'trailer_hook_coupling_cmnt')
			->where('two_report_id',$divisionId)
			->where('two_status',0)
			->first();
        
		$checklistOne = DB::table('tbl_report_insp_check_one')
			->select('one_checklist_id','door_locks_operation','fuel_filler_cover_petrol','glass','molding','bumper_grills','front_bumper','rear_bumper','front_left_headlights','front_right_headlights','rear_left_tail_lights', 'rear_right_tail_lights','general_body_condition','seat_belts','headliner','rearview_mirror','steering_wheel','gear_lever','sun_visor','pillar_trim','armrest_console','floor_mats_carpets','trunk_liner','dashboard','glove_compartment','seats','door_trims','ac_grills','sunroof_shade_liner','spare_tyre','front_left_tyre','back_right_tyre','front_right_tyre','back_left_tyre','coolant_level','coolant_leaks','steering_fluid', 'brake_master_booster','evidence_overheating','coolant_conditions','radiator_cap','radiator_fan','fender_liner','hoses_pipes','cable_harnes_connector','power_steer_fluidlevel','engine_oil_level','external_engine_leaks','engine_mounts','turbo_supercharger','fuel_pump_pipes','cold_starting','fast_idle','noise_level','excess_smoke','inlet_manifold','outlet_manifold','exhaust_pipes','silencer','head_shield_mounting','joints_couplings','engine_underside_leak','catalytic_converter','engine_shield','door_locks_operation_cmnt', 'fuel_filler_cover_petrol_cmnt','glass_cmnt','molding_cmnt','bumper_grills_cmnt','front_bumper_cmnt', 'rear_bumper_cmnt','front_left_headlights_cmnt','front_right_headlights_cmnt','rear_left_tail_lights_cmnt', 'rear_right_tail_lights_cmnt','general_body_condition_cmnt','seat_belts_cmnt','headliner_cmnt', 'rearview_mirror_cmnt','steering_wheel_cmnt','gear_lever_cmnt','sun_visor_cmnt','pillar_trim_cmnt', 'armrest_console_cmnt','floor_mats_carpets_cmnt','trunk_liner_cmnt','dashboard_cmnt','glove_compartment_cmnt','seats_cmnt','door_trims_cmnt','ac_grills_cmnt','sunroof_shade_liner_cmnt','spare_tyre_cmnt', 'front_left_tyre_cmnt','back_right_tyre_cmnt','front_right_tyre_cmnt','back_left_tyre_cmnt', 'coolant_level_cmnt','coolant_leaks_cmnt','steering_fluid_cmnt','brake_master_booster_cmnt', 'evidence_overheating_cmnt','coolant_conditions_cmnt','radiator_cap_cmnt','radiator_fan_cmnt', 'fender_liner_cmnt','hoses_pipes_cmnt','cable_harnes_connector_cmnt','power_steer_fluidlevel_cmnt', 'engine_oil_level_cmnt','external_engine_leaks_cmnt','engine_mounts_cmnt','turbo_supercharger_cmnt', 'fuel_pump_pipes_cmnt','cold_starting_cmnt','fast_idle_cmnt','noise_level_cmnt','excess_smoke_cmnt', 'inlet_manifold_cmnt','outlet_manifold_cmnt','exhaust_pipes_cmnt','silencer_cmnt','head_shield_mounting_cmnt', 'joints_couplings_cmnt','engine_underside_leak_cmnt','catalytic_converter_cmnt','engine_shield_cmnt')
			->where('one_checklist_report_id',$divisionId)
			->where('one_checklist_status',0)
			->first();
			
		$checklistTwo = DB::table('tbl_report_insp_check_two')
			->select('two_checklist_id','gear_selector','gear_shifting','transmission_mount','gear_noise','fluid_level_oil_leak','door_locks','central_locking','ignitionlock_startsys','instrument_panel','headlights','sidelights_runlights','rear_lights','indicator_hazardlights','boot_tailgate_lock','reverse_lights','fog_lights','multimedia','ac_control_cooling','side_mirror','auxiliary_lights','panel_lights','horn','window_operation','sunroof_operation','wipers_jet_washers','keys_remote_controls','warning_lights','number_plate_light','steering_ball_joints','brakes_lines','subframe','power_steering_rack','wheels_hubs_bearings','dampers_bushes','evidencefloor_chassis','engine_performance','gearbox_operation','clutch_operation','steering_operation','brake_operation','hand_parking_brake','drive_train','instru_control_func','suspension_noise','shock_absorber','road_holding_stability','nois','gear_selector_cmnt','gear_shifting_cmnt','transmission_mount_cmnt', 'gear_noise_cmnt','fluid_level_oil_leak_cmnt','door_locks_cmnt','central_locking_cmnt', 'ignitionlock_startsys_cmnt','instrument_panel_cmnt','headlights_cmnt','sidelights_runlights_cmnt', 'rear_lights_cmnt','indicator_hazardlights_cmnt','boot_tailgate_lock_cmnt','reverse_lights_cmnt', 'fog_lights_cmnt','multimedia_cmnt','ac_control_cooling_cmnt','side_mirror_cmnt','auxiliary_lights_cmnt', 'panel_lights_cmnt','horn_cmnt','window_operation_cmnt','sunroof_operation_cmnt','wipers_jet_washers_cmnt', 'keys_remote_controls_cmnt','warning_lights_cmnt','number_plate_light_cmnt','steering_ball_joints_cmnt', 'brakes_lines_cmnt','subframe_cmnt','power_steering_rack_cmnt','wheels_hubs_bearings_cmnt', 'dampers_bushes_cmnt','evidencefloor_chassis_cmnt','engine_performance_cmnt','gearbox_operation_cmnt', 'clutch_operation_cmnt','steering_operation_cmnt','brake_operation_cmnt','hand_parking_brake_cmnt', 'drive_train_cmnt','instru_control_func_cmnt','suspension_noise_cmnt','shock_absorber_cmnt', 'road_holding_stability_cmnt','nois_cmnt')
			->where('two_checklist_report_id',$divisionId)
			->where('two_checklist_status',0)
			->first();
		
        return response()->json(['report'=>$report, 'vehicle'=>$vehicle, 'addspec'=>$addspec, 'warranty'=>$warranty, 'summary'=>$summary, 'summary_type'=>$summary_type, 'vehiclespec'=>$vehiclespec, 'checklist'=>$checklist, 'gallery'=>$gallery, 'gallery_type'=>$gallery_type, 'video'=>$video, 'repfile'=>$repfile, 'dataOne'=>$dataOne, 'dataTwo'=>$dataTwo, 'checklistOne'=>$checklistOne, 'checklistTwo'=>$checklistTwo, 'damageImg'=>$damageImg, 'summary_desc'=>$summary_desc, 'overview'=>$overview]);
    }
	/**************  EDIT END  **************/
	
	/************ View Report Start ************/
	public function viewInspectionReport(Request $request)
    {				
		$data = DB::table('tbl_report')
			->select('report_id','report_reference_no','report_unique_id','report_unique_id_random','report_client_name','report_client_name_ar','report_date_of_inspection','vehicle_info_title','vehicle_info_model_year','vehicle_info_manuf_year','vehicle_info_chassis_no','vehicle_info_odometer','vehicle_info_condition','add_spec_region','add_spec_gearbox','add_spec_fuel_type','add_spec_cylinders','add_spec_engine_size','add_spec_keys','add_spec_doors','add_spec_seats','exte_color_name','inte_color_name','gearbox_type_name','fuel_type_name','steering_side_name','war_service_history','war_service_last','war_service_next','overview_english','overview_arabic')
			->leftJoin('tbl_report_vehicle_info','tbl_report_vehicle_info.vehicle_info_report_id','tbl_report.report_id')
			->leftJoin('tbl_report_additionl_spec','tbl_report_additionl_spec.add_spec_report_id','tbl_report.report_id')
			->leftJoin('tbl_exterior_color','tbl_exterior_color.exte_color_id','tbl_report_additionl_spec.add_spec_exterior_color')
			->leftJoin('tbl_interior_color','tbl_interior_color.inte_color_id','tbl_report_additionl_spec.add_spec_interior_color')
			->leftJoin('tbl_gearbox_type','tbl_gearbox_type.gearbox_type_id','tbl_report_additionl_spec.add_spec_gearbox')
			->leftJoin('tbl_fuel_type','tbl_fuel_type.fuel_type_id','tbl_report_additionl_spec.add_spec_fuel_type')
			->leftJoin('tbl_steering_side','tbl_steering_side.steering_side_id','tbl_report_additionl_spec.add_spec_steering_side')
			->leftJoin('tbl_report_warranty_service','tbl_report_warranty_service.war_service_report_id','tbl_report.report_id')
			->leftJoin('tbl_report_overview','tbl_report_overview.overview_report_id','tbl_report.report_id')
			->where('tbl_report.report_id',$request->report_id)
			->first();
 
        return view ('inspectionreport::inspectionReportView',compact('data'));
    }
	
	/************** DELETE **************/
	public function deleteInspectionReport(Request $request)
    {
        $divisionId = $request->report_id;  //dd($divisionId);
        $data = array('report_status' => strip_tags('1'));
        
        $input = DB::table('tbl_report')
			->where(['report_id' => $divisionId])
			->update($data);

        if ($input) 
		{
            //Logs & Activity Manager.
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id   = Auth::user()->id;
            $activity  = 'Inspection Report Has been Deleted By '. $user_name.' ';
            $log_array = array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);
            Core::userActivityAction($log_array);
            return response()->json(['status' => 1, 'msg' => 'Report Deleted successfully!', 'heading' => 'Success']);
        }
    }
	
    public function viewReportsIndex()
	{
		// return view('inspectionreport::ViewInspectionReports');
		$exte_color = DB::table('tbl_exterior_color')
				->select('exte_color_id','exte_color_name')                        
				->where('exte_color_status',0)
				->where('exte_color_publish_status',1)
				->get();
				
		$inte_color = DB::table('tbl_interior_color')
				->select('inte_color_id','inte_color_name')                        
				->where('inte_color_status',0)
				->where('inte_color_publish_status',1)
				->get();
				
		$gear_box = DB::table('tbl_gearbox_type')
				->select('gearbox_type_id','gearbox_type_name')                        
				->where('gearbox_type_status',0)
				//->where('gearbox_type_publish_status',2)
				->get();
				
		$fuel_type = DB::table('tbl_fuel_type')
				->select('fuel_type_id','fuel_type_name')                        
				->where('fuel_type_status',0)
				//->where('fuel_type_publish_status',2)
				->get();
		
		$steer_side = DB::table('tbl_steering_side')
				->select('steering_side_id','steering_side_name')                        
				->where('steering_side_status',0)
				->get();	

		$summ_type = DB::table('tbl_summary_type')
				->select('summary_type_id','summary_type_name')                        
				->where('summary_type_status',0)
				->get();
	
        return view('inspectionreport::ViewInspectionReports')->with(['exte_color'=>$exte_color,'inte_color'=>$inte_color,'gear_box'=>$gear_box,'fuel_type'=>$fuel_type,'steer_side'=>$steer_side,'summ_type'=>$summ_type]);
	}
	 
	/********** Datatable View approved reports **********/
	public function viewReportDatatable(Request $request)
	{  
		ini_set('memory_limit', '-1');        
        $current_route = \Route::current()->uri();
		
		$privilege = Auth::user()->previlage;
		$main_id = 45;
		$sub_id = 101;
		$option = DB::table('tbl_menu_set_options')
				->select('opset_options')
				->where('opset_privilege',$privilege)
				->where('opset_main_id',$main_id)
				->where('opset_sub_id',$sub_id)
				->first();

		$limit   = ($request->length != '') ? $request->length : 10;
		$offset  = ($request->start != '') ? $request->start : 0;
		$search  =  $request->search['value'];
		$user_id = Auth::user()->user_id;
		 
		$area = DB::table('tbl_report')
			->select('report_id','report_reference_no','report_client_name','report_date_of_inspection','report_expired_status','users.name','tbl_lead.lead_id','tbl_lead.lead_followup_type','lead_assigned_status')
			->where('report_status',0)   
            ->leftjoin('users', 'users.id' ,'=','tbl_report.report_addedby')
            ->leftjoin('tbl_lead', 'tbl_lead.lead_id' ,'=','tbl_report.report_lead_id')
            ->orderBy('tbl_report.report_id','desc')
            ->Where(function($query) use ($search) 
                    {
						$query->where('report_reference_no', 'like', $search . '%');
						$query->orwhere('report_client_name', 'like', $search . '%');
						$query->orwhere('lead_assigned_status', 'like', $search . '%');
						$query->orwhere('report_expired_status', 'like', $search . '%');
						$query->orwhere('name', 'like', $search . '%');
                    });

		if($privilege != 1 && $privilege != 2 && $privilege != 48)
		{
			$area->where('report_addedby',Auth::user()->id);
		}
	 
		if($privilege == 48)
		{   
			$area->where('lead_added_by',Auth::user()->id);
		}
		
		$data = ["iTotalDisplayRecords" => $area->count(), "iTotalRecords" => $area->count(), "TotalDisplayRecords" => $limit,'option'=> $option];
		$dataMod = $area->skip($offset)->take($limit)->get();  
		$data['data'] = $dataMod->toArray();  
		return response()->json($data);
	}
	 
	public function getStatusAction()
    {  
		$user_previlage = Auth::user()->previlage;	
		$staff = array();		
		$staffData = DB::table('tbl_followup_type')
				->select('followup_type_name','followup_type_id')
				->where('followup_type_status',0)
				->where('followup_type_name','!=' ,'New')
				->orderby('followup_type_priority','asc');
 
		if($user_previlage === 48 || $user_previlage === 49)
		{ 
			$staffData = $staffData->where('followup_type_name','!=' ,'Assign');  
			$staffData = $staffData->where('followup_type_name','!=' ,'Reassign');  
			$staffData = $staffData->where('followup_type_name','!=' ,'Approved'); 
			$staffData = $staffData->where('followup_type_name','!=' ,'Plan / Shedule'); 
			$staffData = $staffData->where('followup_type_name','!=' ,'Reshedule');  
			$staffData = $staffData->where('followup_type_name','!=' ,'Closed');  
			$staffData = $staffData->where('followup_type_name','!=' ,'Rejected');  
			$staffData = $staffData->where('followup_type_name','!=' ,'Inspection');  
		} 	
		if($user_previlage === 1 || $user_previlage === 2)
		{
			$staffData = $staffData->where('followup_type_name','!=' ,'Assign');  
			$staffData = $staffData->where('followup_type_name','!=' ,'Reassign');  
 			$staffData = $staffData->where('followup_type_name','!=' ,'Plan / Shedule');  
			$staffData = $staffData->where('followup_type_name','!=' ,'Reshedule'); 
			$staffData = $staffData->where('followup_type_name','!=' ,'Closed');  
			//$staffData = $staffData->where('followup_type_name','!=' ,'Rejected');  
			$staffData = $staffData->where('followup_type_name','!=' ,'Inspection');  
			$staffData = $staffData->where('followup_type_name','!=' ,'Inspection Completed');  
		}
		$staffData = $staffData->get()->toArray();  

		return response()->json(['status' => 1, 'result'=>$staffData]);
    }
    
    public function SummaryDes(Request $request)
	{
	    $data = DB::table('tbl_summary_description')
			->where('sum_desc_type',$request->SummaryType)
			->orderBy('sum_desc_name', 'asc')->pluck('sum_desc_name','sum_desc_id'); //dd($data);
			
		return $data;
	}

}