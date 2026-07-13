<?php

namespace Modules\Manage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Core;
use Illuminate\Support\Facades\Validator;
use DataTables,Auth,DB;

class ModelController extends Controller
{
    public function index(Request $request)
    {
        return view('manage::model_index');
    }
    
    public function createAction(Request $request)
    {   
        $previlage = Auth::user()->id;
        
        $data = [
                'model_ip'     => $request->ip(),
                'model_addedby'=> $previlage,
                'model_status' => '0',
                'model_make'   => $request->model_make,
                'model_name'   => $request->model_name,
            ];
                
        $validator = validator::make($data,['model_make'=>'required','model_name'=>'required']);
			
    	if($validator->fails()) 
		{
            foreach (array_values($validator->messages()->toArray()) as $msg) 
			{
                $error = implode(' ', $msg) . '<br>';
            }
            return response()->json(['status' => 0, 'msg' => $error]);
        } 
		else 
		{ 
			$input = DB::table('tbl_model')->insertGetId($data);
         
			if ($input) 
			{
				//Logs & Activity Manager.
				$ip = $request->ip();
				$action = '';
				$user_name = Auth::user()->name;
				$user_id = Auth::user()->id;
				$category = "New Car Model";
				$activity = 'New Car Model '.$data['model_name'].' Has been Added By '.$user_name.' ';
				$log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
				Core::userActivityAction($log_array);
				
				return response()->json(['status' => 1, 'msg' => 'Model added successfully!', 'heading' => 'Success']);
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
        $colName = 'tbl_model.model_id';
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

        $division = DB::table('tbl_model')
                ->select('model_id','model_name','model_publish_status','tbl_make.make_name','users.name')
                ->join('users', 'users.id', '=', 'tbl_model.model_addedby')
                ->leftjoin('tbl_make', 'tbl_make.make_id', '=', 'tbl_model.model_make')
                ->Where(function ($query) use ($search) 
                        {
							$query->where('tbl_model.model_name', 'like', $search . '%');
							$query->orwhere('tbl_make.make_name', 'like', $search . '%');
                        });

        $division->where('model_status', '0');
        $division->orderBy('tbl_model.model_id', 'DESC');
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
        $divisionId = $request->model_id;
         
        $data  = DB::table('tbl_model')
                ->where('tbl_model.model_id', $divisionId)
                ->first();

        return response()->json(['model_status' => 0,'data'=>$data]);
    }
    
    public function editDivisionAction(Request $request)
    {
        $id = $request->model_id;         
        $previlage = Auth::user()->id;
        $data = [
                'model_ip'     => $request->ip(),
                'model_addedby'=> $previlage,
                'model_status' => '0',
                'model_name'   => $request->model_name,
                'model_make'   => $request->model_make,
            ];
     
        $input = DB::table('tbl_model')->where('model_id',$id)->update($data);   
    
        if($input) 
        {
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $category = "Update Model";
            $activity = 'New Model '.$data['model_name'].' Has been updated By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);
            
            return response()->json(['status' => 1, 'msg' => 'Model Updated successfully!', 'heading' => 'Success']);
        }
        else
        {
            return redirect('clients')->withInput()->with('info', 'No changes found!');
        }
    }
   
    public function deleteDivisionAction(Request $request)
    {
        $id = $request->model_id;
        $clients = DB::table('tbl_model')
            ->where('model_id',$id)
            ->first();
        
        if($clients)
        {
            $model_id = $clients->model_id;
            DB::table('tbl_model')->where('model_id',$id)->update(['model_status'=>1]);
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $category = "Delete Model";
            $activity = 'Model No'.strip_tags($model_id).' Has been Deleted By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);
             
            return response()->json(['status' => 1, 'msg' => 'Car Model Deleted Successfully!', 'heading' => 'Success']);
        }
        else
        {
            return response()->json(['heading'=>'Error','text'=>'Car Model not found','icon'=>'error']);
        }
    }
    
    public function status(request $request)
    {
        $id = $request->id;
        
        $clients = DB::table('tbl_model')
            ->where('model_id', $id)
            ->first();   

        $status = DB::select('select model_publish_status from tbl_model where model_id ='.$id);
        if($status[0]->model_publish_status == 1)
        { 
            $change = 2;
        }
        else
        {
            $change = 1; 
        }
        
        $update = array('model_publish_status' => $change,'model_ip' => $request->ip());
        $input = DB::table('tbl_model')
            ->where('model_id', $id)
            ->update(['model_publish_status' => $change,'model_ip' => $request->ip()]);
         
        if($input)          
        {
            //Logs & Activity Manager.
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $activity = 'Model Status'.$clients->model_name.' Has been updated By '. $user_name.' ';
            $log_array = array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
            Core::userActivityAction($log_array);
            return response()->json(['status' => 1, 'msg' => 'Model Status Changed Successfully!', 'heading' => 'Success']);
        }

        return response()->json(['status' => 1,'msg' => 'Model Status Changed Successfully','heading' => 'Success']);
    }
}
