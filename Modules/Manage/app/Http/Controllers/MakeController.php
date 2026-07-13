<?php

namespace Modules\Manage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Core;
use Modules\Privilege\Models\PrivilegeModel;
use Modules\Rules\Models\SetoptionsModel;
use Illuminate\Support\Facades\Validator;
use DataTables,Auth,DB;

class MakeController extends Controller
{
    public function index()
    {
        return view('manage::make_index');
    }
	
	public function createAction(Request $request)
    {   
        $previlage = Auth::user()->id;
        
        $data = [
                'make_ip'      => $request->ip(),
                'make_addedby' => $previlage,
                'make_status'  => '0',
                'make_date'    => date('Y-m-d'),
                'make_name'    => $request->make_name,
            ];
        		
		$validator = validator::make($data,['make_name'=>'required']);
			
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
			$input = DB::table('tbl_make')->insertGetId($data);
			 
			if ($input) 
			{
				//Logs & Activity Manager.
				$ip = $request->ip();
				$action = '';
				$user_name = Auth::user()->name;
				$user_id = Auth::user()->id;
				$category = "New Make";
				$activity = 'New Make'.$data['make_name'].' Has been Added By '.$user_name.' ';
				$log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
				Core::userActivityAction($log_array);
				 
				return response()->json(['status' => 1, 'msg' => 'Make added successfully!', 'heading' => 'Success']);
			}
			else
			{
				return redirect('manage/make/')->withInput()->with('error', 'Failed to create new Make! Try again.');
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
        $colName = 'tbl_make.make_id';
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

        $division = DB::table('tbl_make')
                ->select('make_id','make_name','make_publish_status','users.name')
                ->join('users', 'users.id', '=', 'tbl_make.make_addedby')
                ->Where(function ($query) use ($search) 
                        {
                            $query->where('tbl_make.make_name', 'like', $search . '%');
                        });

        $division->where('make_status', '0');
        $division->orderBy('tbl_make.make_id', 'DESC');
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
        $divisionId = $request->make_id;
        
        $data  = DB::table('tbl_make')
                ->where('tbl_make.make_id', $divisionId)
                ->first();

        return response()->json(['make_status' => 0,'data'=>$data]);
    }
    
    public function editDivisionAction(Request $request)
    {
        $id = $request->make_id;         
        $previlage = Auth::user()->id;
        $data = [
				'make_status'  => '0',
                'make_ip'      => $request->ip(),
                'make_addedby' => $previlage,
                'make_name'    => $request->make_name,
				];
      
        $input = DB::table('tbl_make')->where('make_id',$id)->update($data);   
    
        if($input) 
        {
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $category = "Update Make";
            $activity = 'New Make'.$data['make_name'].' Has been updated By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);
             
            return response()->json(['status' => 1, 'msg' => 'Make Updated successfully!', 'heading' => 'Success']);
        }
        else
        {
            return redirect('clients')->withInput()->with('info', 'No changes found!');
        }
    }
   
    public function deleteDivisionAction(Request $request)
    {
        $id = $request->make_id;
        $clients = DB::table('tbl_make')
            ->where('make_id',$id)
            ->first();
        
        if($clients)
        {
            $make_id = $clients->make_id;
            DB::table('tbl_make')->where('make_id',$id)->update(['make_status'=>1]);
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $category = "Delete Make";
            $activity = 'Make No'.strip_tags($make_id).' Has been Deleted By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);

            return response()->json(['status' => 1, 'msg' => 'Make Deleted Successfully!', 'heading' => 'Success']);
        }
        else
        {
            return response()->json(['heading'=>'Error','text'=>'Make not found','icon'=>'error']);
        }
    }
    
    public function status(request $request)
    {
        $id = $request->id;
        
        $clients = DB::table('tbl_make')
            ->where('make_id', $id)
            ->first();   
            
        $status = DB::select('select make_publish_status from tbl_make where make_id ='.$id);
        if($status[0]->make_publish_status == 1)
        { 
            $change = 2;
        }
        else
        {
            $change = 1; 
        }
        
        $update = array('make_publish_status' => $change,'make_ip' => $request->ip());
        $input = DB::table('tbl_make')
            ->where('make_id', $id)
            ->update(['make_publish_status' => $change, 'make_ip' => $request->ip()]);
         
        if($input)          
        {
            //Logs & Activity Manager.
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $activity = 'Make Status'.$clients->make_name.' Has been updated By '. $user_name.' ';
            $log_array = array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
            Core::userActivityAction($log_array);
            return response()->json(['status' => 1, 'msg' => 'Make Status Changed Successfully!', 'heading' => 'Success']);
        }

        return response()->json(['status' => 1,'msg' => 'Make Status Changed Successfully','heading' => 'Success']);
    }
}
