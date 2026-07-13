<?php

namespace Modules\Manage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Core;
use Illuminate\Support\Facades\Validator;
use DataTables,Auth,DB;

class ExteriorColorController extends Controller
{
	public function index()
    {
		$privilege = Auth::user()->previlage;
		$main_id = 40;
       	$sub_id  = 99;
		
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
		
		if(in_array($main_id,json_decode($permission[0]->alloted_mainmenus)) && in_array($sub_id,json_decode($permission[0]->alloted_submenus)))
		{                    
			return view('manage::exteriorColor_index',['option'=>$option]);
		}
		else
		{
			//return view('dashboard');
		}  		
    }
	
	public function createAction(Request $request)
    {   
        $previlage = Auth::user()->id;
        
        $data = [
                'exte_color_ip'         => $request->ip(),
                'exte_color_addedby'    => $previlage,
                'exte_color_status'     => '0',
                'exte_color_name'       => $request->exte_color_name,
                'exte_color_name_arabic'=> $request->exte_color_name_arabic,
            ];
        		
		$validator = validator::make($data,['exte_color_name'=>'required']);
			
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
			$input = DB::table('tbl_exterior_color')->insertGetId($data);
			 
			if ($input) 
			{
				//Logs & Activity Manager.
				$ip = $request->ip();
				$action = '';
				$user_name = Auth::user()->name;
				$user_id = Auth::user()->id;
				$category = "New Exterior Color";
				$activity = 'New Exterior Color'.$data['exte_color_name'].' Has been Added By '.$user_name.' ';
				$log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
				Core::userActivityAction($log_array);
				 
				return response()->json(['status' => 1, 'msg' => 'Exterior Color added successfully!', 'heading' => 'Success']);
			}
		}			
    }
    
    public function dataTable(Request $request)
    {
		$privilege = Auth::user()->previlage;
		$main_id = 40;
       	$sub_id  = 99;

		$option = DB::table('tbl_menu_set_options')
                ->select('opset_options')
                ->where('opset_privilege',$privilege)
                ->where('opset_main_id',$main_id)
                ->where('opset_sub_id',$sub_id)
                ->first();

        $limit = ($request->length != '') ? $request->length : 10;
        $offset = ($request->start != '') ? $request->start : 0;
        $search = $request->search['value'];
        $order  = $request->order;
        $columns = $request->columns;
        $colName = 'tbl_exterior_color.exte_color_id';
        $sort = 'ASC';
        
        if (isset($order[0]['column']) && isset($order[0]['dir'])) 
        {
            $colNo = $order[0]['column'];
            $sort = $order[0]['dir'];
            if (isset($columns[$colNo]['name'])) 
            {
                $colName = $columns[$colNo]['name'];
            }
        }

        $division = DB::table('tbl_exterior_color')
                ->select('exte_color_id','exte_color_name','exte_color_name_arabic','exte_color_publish_status','users.name')
                ->leftjoin('users', 'users.id', '=', 'tbl_exterior_color.exte_color_addedby')
                ->Where(function ($query) use ($search) 
                        {
                            $query->where('tbl_exterior_color.exte_color_name', 'like', $search . '%');
                            $query->orwhere('tbl_exterior_color.exte_color_name_arabic', 'like', $search . '%');
                        });

        $division->where('exte_color_status', '0');
        $division->orderBy('tbl_exterior_color.exte_color_id', 'DESC');
        if ($colName != '' && $sort != '') 
        {
            $division->orderBy($colName, $sort);
        }
		
        $data = ["iTotalDisplayRecords" => $division->count(), "iTotalRecords" => $division->count(), "TotalDisplayRecords" => $limit,'option'=> $option];
        $data['data'] = $division->skip($offset)->take($limit)->get()->toArray();

        return response()->json($data);
    }
    
    public function getDivisionAction(Request $request)
    {
        $divisionId = $request->exte_color_id;

        $data  = DB::table('tbl_exterior_color')
                ->where('tbl_exterior_color.exte_color_id', $divisionId)
                ->first();

        return response()->json(['exte_color_status' => 0,'data'=>$data]);
    }
    
    public function editDivisionAction(Request $request)
    {  	
        $id = $request->exte_color_id;           
        $previlage = Auth::user()->id;
        $data = ['exte_color_status'     => '0',
                 'exte_color_ip'         => $request->ip(),
                 'exte_color_editedby'   => $previlage,
                 'exte_color_name'       => $request->exte_color_name,
                 'exte_color_name_arabic'=> $request->exte_color_name_arabic,
				];
      
        $input = DB::table('tbl_exterior_color')->where('exte_color_id',$id)->update($data);   
    
        if($input) 
        {
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $category = "Update Exterior Color";
            $activity = 'New Exterior Color'.$data['exte_color_name'].' Has been updated By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);

            return response()->json(['status' => 1, 'msg' => 'Exterior Color Updated successfully!', 'heading' => 'Success']);
        }
    }
   
    public function deleteDivisionAction(Request $request)
    {
        $id = $request->exte_color_id;
        $clients = DB::table('tbl_exterior_color')
            ->where('exte_color_id',$id)
            ->first();
        
        if($clients)
        {
            $exte_color_id = $clients->exte_color_id;
            DB::table('tbl_exterior_color')->where('exte_color_id',$id)->update(['exte_color_status'=>1]);
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $category = "Delete Exterior Color";
            $activity = 'Exterior Color No'.strip_tags($exte_color_id).' Has been Deleted By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);

            return response()->json(['status' => 1, 'msg' => 'Exterior Color Deleted Successfully!', 'heading' => 'Success']);
        }
        else
        {
            return response()->json(['heading'=>'Error','text'=>'Exterior Color not found','icon'=>'error']);
        }
    }
    
    public function status(request $request)
    {
        $id = $request->id;
        
        $clients = DB::table('tbl_exterior_color')
            ->where('exte_color_id', $id)
            ->first();   
            
        $status = DB::select('select exte_color_publish_status from tbl_exterior_color where exte_color_id ='.$id);
        if($status[0]->exte_color_publish_status == 1)
        { 
            $change = 2;
        }
        else
        {
            $change = 1; 
        }
        
        $update = array('exte_color_publish_status' => $change,'exte_color_ip' => $request->ip());
        $input = DB::table('tbl_exterior_color')
            ->where('exte_color_id', $id)
            ->update(['exte_color_publish_status' => $change, 'exte_color_ip' => $request->ip()]);
         
        if($input)          
        {
            //Logs & Activity Manager.
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $activity = 'Exterior Color Status'.$clients->exte_color_name.' Has been updated By '. $user_name.' ';
            $log_array = array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
            Core::userActivityAction($log_array);
            return response()->json(['status' => 1, 'msg' => 'Exterior Color Status Changed Successfully!', 'heading' => 'Success']);
        }

        return response()->json(['status' => 1,'msg' => 'Exterior Color Status Changed Successfully','heading' => 'Success']);
    }
}
