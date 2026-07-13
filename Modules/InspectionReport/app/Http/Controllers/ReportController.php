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

class ReportController extends Controller
{
	public function newreportIndex()
    {
		$exte_color = DB::table('tbl_exterior_color')
				->select('exte_color_id','exte_color_name')                        
				->where('exte_color_status',0)
				//->where('exte_color_publish_status',2)
				->get();
				
		$inte_color = DB::table('tbl_interior_color')
				->select('inte_color_id','inte_color_name')                        
				->where('inte_color_status',0)
				//->where('inte_color_publish_status',2)
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
				
        return view('inspectionreport::InspectionReportIndex')->with(['exte_color'=>$exte_color,'inte_color'=>$inte_color,'gear_box'=>$gear_box,'fuel_type'=>$fuel_type,'steer_side'=>$steer_side,'summ_type'=>$summ_type]);
    }
	
	
    /**
     * Display a listing of the resource.
     */
    /*public function testindex(Request $request)
    { 
		return view('inspectionreport::add_test_properties'); 
    }*/
	
	public function add_test_test(Request $request)
    { 
		return view('inspectionreport::add_test_test'); 
    }
	
    public function index_test()
    {
		$exte_color = DB::table('tbl_exterior_color')
				->select('exte_color_id','exte_color_name')                        
				->where('exte_color_status',0)
				//->where('exte_color_publish_status',2)
				->get();
				
		$inte_color = DB::table('tbl_interior_color')
				->select('inte_color_id','inte_color_name')                        
				->where('inte_color_status',0)
				//->where('inte_color_publish_status',2)
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
				
        return view('inspectionreport::add_test_properties')->with(['exte_color'=>$exte_color,'inte_color'=>$inte_color,'gear_box'=>$gear_box,'fuel_type'=>$fuel_type,'steer_side'=>$steer_side,'summ_type'=>$summ_type]);
    }
	
	
	/********** GeneralInfo **********/
	public function addGeneralInfo_test(Request $request)
    {  //dd(1);
        $reference_no       = $request->report_reference_no; 
        $client_name        = $request->report_client_name;
        $date_of_inspection = $request->report_date_of_inspection;
        $edit_id            = $request->edit_id;  // dd($edit_id);
		
        $data = array(
				'report_ip'      => $request->ip(),
				'report_addedby' => Auth::user()->id,
                'report_status'  => 0, 
				'report_reference_no' => $reference_no, 				
				'report_client_name'  => $client_name, 				
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
	public function add_vehicleInfo_test(Request $request)
    {   
        $edit_id   = $request->edit_id;     //dd($edit_id);
        $update_id = $request->update_id;      //dd($update_id);
		
        $data = array(
				'vehicle_info_status'     => 0, 
				'vehicle_info_addedby'    => Auth::user()->id,
				'vehicle_info_ip'         => $request->ip(),
				'vehicle_info_title'      => $request->vehicle_info_title,			
				'vehicle_info_model_year' => $request->vehicle_info_model_year,				
				'vehicle_info_manuf_year' => $request->vehicle_info_manuf_year,		
				'vehicle_info_chassis_no' => $request->vehicle_info_chassis_no,		
				'vehicle_info_odometer'   => $request->vehicle_info_odometer,		
				'vehicle_info_condition'  => $request->vehicle_info_condition,		
                );
           
		if($update_id == '' || $update_id == null)
		{
			$insertid = DB::table('tbl_report_vehicle_info')->insertGetId($data);
 		}
		else 
		{	 
			$data['vehicle_info_editedby'] = Auth::user()->id;
			$update = DB::table('tbl_report_vehicle_info')
					->where('vehicle_info_report_id',$edit_id)
					->update($data);
			
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
	
	
	
	
	/********** Datatable **********/
	public function getDatatable_test(Request $request)
	{ 
	//alert("hi");
		$limit  = ($request->length != '') ? $request->length : 10;
        $offset = ($request->start != '') ? $request->start : 0;
        $search = $request->search['value'];
        $order  = $request->order;
        $columns = $request->columns;
        $colName = 'tbl_report.report_id';
        $sort = 'ASC';
        
        if (isset($order[0]['column']) && isset($order[0]['dir'])) {
            $colNo = $order[0]['column'];
            $sort = $order[0]['dir'];
            if (isset($columns[$colNo]['name'])) {
                $colName = $columns[$colNo]['name'];
            }
        }
		 $division = DB::table('tbl_report')
				->where('report_status',0)   
				->select('report_id','report_reference_no','report_client_name','report_date_of_inspection','users.name')
                ->leftjoin('users', 'users.id' ,'=','tbl_report.report_addedby')
                ->orderBy('tbl_report.report_id','desc')
                ->Where(function($query) use ($search) 
                      {
							//$query->where('qp_subject', 'like', $search . '%');
                      });
 				
		 if ($colName != '' && $sort != '') {
            $division->orderBy($colName, $sort);
        } else {
             $division->orderBy('tbl_report.report_id', 'asc');
        }
        $data = ["iTotalDisplayRecords" => $division->count(), "iTotalRecords" => $division->count(), "TotalDisplayRecords" => $limit];
        $data['data'] = $division->skip($offset)->take($limit)->get()->toArray();
        
        return response()->json($data);
	}
	 
     
}
