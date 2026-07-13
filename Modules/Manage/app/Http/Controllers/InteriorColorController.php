<?php

namespace Modules\Manage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Core;
use Modules\Privilege\Models\PrivilegeModel;
use Modules\Rules\Models\SetoptionsModel;
use Illuminate\Support\Facades\Validator;
use DataTables,Auth,DB;

class InteriorColorController extends Controller
{
	public function index()
    {
        return view('manage::interiorColor_index');
    }
	
	public function createAction(Request $request)
    {   
        $previlage = Auth::user()->id;
        
        $data = [
                'inte_color_ip'      => $request->ip(),
                'inte_color_addedby' => $previlage,
                'inte_color_status'  => '0',
                'inte_color_name'    => $request->inte_color_name,
                'inte_color_name_arabic' => $request->inte_color_name_arabic,
            ];
        		
		$validator = validator::make($data,['inte_color_name'=>'required']);
			
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
			$input = DB::table('tbl_interior_color')->insertGetId($data);
			 
			if ($input) 
			{
				//Logs & Activity Manager.
				$ip = $request->ip();
				$action = '';
				$user_name = Auth::user()->name;
				$user_id = Auth::user()->id;
				$category = "New Interior Color";
				$activity = 'New Interior Color'.$data['inte_color_name'].' Has been Added By '.$user_name.' ';
				$log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
				Core::userActivityAction($log_array);
				 
				return response()->json(['status' => 1, 'msg' => 'Interior Color added successfully!', 'heading' => 'Success']);
			}
		}			
    }
    
    public function dataTable(Request $request)
    {
        $limit = ($request->length != '') ? $request->length : 10;
        $offset = ($request->start != '') ? $request->start : 0;
        $search = $request->search['value'];
        $order = $request->order;
        $columns = $request->columns;
        $colName = 'tbl_interior_color.inte_color_id';
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

        $division = DB::table('tbl_interior_color')
                ->select('inte_color_id','inte_color_name','inte_color_name_arabic','inte_color_publish_status','users.name')
                ->leftjoin('users', 'users.id', '=', 'tbl_interior_color.inte_color_addedby')
                ->Where(function ($query) use ($search) 
                        {
                            $query->where('tbl_interior_color.inte_color_name', 'like', $search . '%');
                            $query->orwhere('tbl_interior_color.inte_color_name_arabic', 'like', $search . '%'); 
                        });

        $division->where('inte_color_status', '0');
        $division->orderBy('tbl_interior_color.inte_color_id', 'DESC');
        if ($colName != '' && $sort != '') 
        {
            $division->orderBy($colName, $sort);
        }
        $data = ["iTotalDisplayRecords" => $division->count(), "iTotalRecords" => $division->count(), "TotalDisplayRecords" => $limit];
        $data['data'] = $division->skip($offset)->take($limit)->get()->toArray();
         
        return response()->json($data);
    }
    
    public function getDivisionAction(Request $request)
    {
        $divisionId = $request->inte_color_id;
        $data  = DB::table('tbl_interior_color')
                ->where('tbl_interior_color.inte_color_id', $divisionId)
                ->first();
  
        return response()->json(['inte_color_status' => 0,'data'=>$data]);
    }
    
    public function editDivisionAction(Request $request)
    {  	
        $id = $request->inte_color_id;           
        $previlage = Auth::user()->id;
        $data = ['inte_color_status'  => '0',
                 'inte_color_ip'      => $request->ip(),
                 'inte_color_addedby' => $previlage,
                 'inte_color_name'    => $request->inte_color_name,
                 'inte_color_name_arabic' => $request->inte_color_name_arabic,
				];
      
        $input = DB::table('tbl_interior_color')->where('inte_color_id',$id)->update($data);   
    
        if($input) 
        {
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $category = "Update Interior Color";
            $activity = 'New Interior Color'.$data['inte_color_name'].' Has been updated By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);

            return response()->json(['status' => 1, 'msg' => 'Interior Color Updated successfully!', 'heading' => 'Success']);
        }
    }
   
    public function deleteDivisionAction(Request $request)
    {
        $id = $request->inte_color_id;
        $clients = DB::table('tbl_interior_color')
            ->where('inte_color_id',$id)
            ->first();
        
        if($clients)
        {
            $inte_color_id = $clients->inte_color_id;
            DB::table('tbl_interior_color')->where('inte_color_id',$id)->update(['inte_color_status'=>1]);
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $category = "Delete Interior Color";
            $activity = 'Interior Color No'.strip_tags($inte_color_id).' Has been Deleted By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);

            return response()->json(['status' => 1, 'msg' => 'Interior Color Deleted Successfully!', 'heading' => 'Success']);
        }
        else
        {
            return response()->json(['heading'=>'Error','text'=>'Interior Color not found','icon'=>'error']);
        }
    }
    
    public function status(request $request)
    {
        $id = $request->id;
        $clients = DB::table('tbl_interior_color')
            ->where('inte_color_id', $id)
            ->first();   
            
        $status = DB::select('select inte_color_publish_status from tbl_interior_color where inte_color_id ='.$id);
        if($status[0]->inte_color_publish_status == 1)
        { 
            $change = 2;
        }
        else
        {
            $change = 1; 
        }
        
        $update = array('inte_color_publish_status' => $change,'inte_color_ip' => $request->ip());
        $input = DB::table('tbl_interior_color')
            ->where('inte_color_id', $id)
            ->update(['inte_color_publish_status' => $change, 'inte_color_ip' => $request->ip()]);
         
        if($input)          
        {
            //Logs & Activity Manager.
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $activity = 'Interior Color Status'.$clients->inte_color_name.' Has been updated By '. $user_name.' ';
            $log_array = array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
            Core::userActivityAction($log_array);
            return response()->json(['status' => 1, 'msg' => 'Interior Color Status Changed Successfully!', 'heading' => 'Success']);
        }

        return response()->json(['status' => 1,'msg' => 'Interior Color Status Changed Successfully','heading' => 'Success']);
    }
}
