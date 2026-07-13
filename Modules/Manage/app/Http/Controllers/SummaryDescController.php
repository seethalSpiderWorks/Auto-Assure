<?php

namespace Modules\Manage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Core;
use Illuminate\Support\Facades\Validator;
use DataTables,Auth,DB;

class SummaryDescController extends Controller
{
    public function index(Request $request)
    {
		$summ_type = DB::table('tbl_summary_type')
				->select('summary_type_id','summary_type_name')                        
				->where('summary_type_status',0)
				->get();
 
		return view('manage::summaryDesc_index')->with(['summ_type'=>$summ_type]);
    }
    
    public function createAction(Request $request)
    {   
        $previlage = Auth::user()->id;
        $data = [
                'sum_desc_ip'     => $request->ip(),
                'sum_desc_addedby'=> $previlage,
                'sum_desc_status' => '0',
				'sum_desc_date'   => date('Y-m-d'),
                'sum_desc_type'   => $request->sum_desc_type,
                'sum_desc_name'   => trim($request->sum_desc_name),
                'sum_desc_name_ar'=> trim($request->sum_desc_name_ar),
            ];
			
        $validator = validator::make($data,['sum_desc_type'=>'required','sum_desc_name'=>'required']);
			
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
			$input = DB::table('tbl_summary_description')->insertGetId($data);
         
			if ($input) 
			{
				//Logs & Activity Manager.
				$ip = $request->ip();
				$action = '';
				$user_name = Auth::user()->name;
				$user_id = Auth::user()->id;
				$category = "New Summary Description";
				$activity = 'New Summary Description '.$data['sum_desc_name'].' Has been Added By '.$user_name.' ';
				$log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
				Core::userActivityAction($log_array);
				
				return response()->json(['status'=>1,'msg'=>'Summary Description added successfully!','heading'=>'Success']);
			}
		}
    }
    
    public function dataTable(Request $request)
    {
        $limit  = ($request->length != '') ? $request->length : 10;
        $offset = ($request->start != '') ? $request->start : 0;
        $search = $request->search['value'];
        $order = $request->order;
        $columns = $request->columns;
        $colName = 'tbl_summary_description.sum_desc_id';
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

        $division = DB::table('tbl_summary_description')
                ->select('sum_desc_id','sum_desc_type','sum_desc_name','tbl_summary_type.summary_type_name','users.name')
                ->leftjoin('users', 'users.id', '=', 'tbl_summary_description.sum_desc_addedby')
                ->leftjoin('tbl_summary_type', 'tbl_summary_type.summary_type_id', '=', 'tbl_summary_description.sum_desc_type')
                ->Where(function ($query) use ($search) 
                        {
							$query->where('tbl_summary_description.sum_desc_name', 'like', $search . '%');
							$query->orwhere('tbl_summary_type.summary_type_name', 'like', $search . '%');
							$query->orwhere('users.name', 'like', $search . '%');
                        });

        $division->where('sum_desc_status', '0');
        $division->orderBy('tbl_summary_description.sum_desc_id', 'DESC');
        if ($colName != '' && $sort != '') 
        {
            $division->orderBy($colName, $sort);
        }
        $data = ["iTotalDisplayRecords"=> $division->count(), "iTotalRecords" => $division->count(), "TotalDisplayRecords" => $limit];
        $data['data'] = $division->skip($offset)->take($limit)->get()->toArray();
        
        return response()->json($data);
    }
    
    public function getDivisionAction(Request $request)
    {
        $divisionId = $request->sum_desc_id;

        $data  = DB::table('tbl_summary_description')
                ->where('tbl_summary_description.sum_desc_id', $divisionId)
                ->first(); 

        return response()->json(['sum_desc_status' => 0,'data'=>$data]);
    }
    
    public function editDivisionAction(Request $request)
    {
        $id = $request->sum_desc_id;         
        $previlage = Auth::user()->id;
        $data = [
                'sum_desc_ip'      => $request->ip(),
                'sum_desc_editedby'=> $previlage,
                'sum_desc_status'  => '0',
                'sum_desc_name'    => trim($request->sum_desc_name),
                'sum_desc_type'    => trim($request->sum_desc_type),
            ];
     
        $input = DB::table('tbl_summary_description')->where('sum_desc_id',$id)->update($data);   
    
        if($input) 
        {
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $category = "Update Summary Description";
            $activity = 'New Summary Description '.$data['sum_desc_name'].' Has been updated By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);
            
            return response()->json(['status' => 1, 'msg' => 'Summary Description Updated successfully!', 'heading' => 'Success']);
        }
        else
        {
            return redirect('clients')->withInput()->with('info', 'No changes found!');
        }
    }
   
    public function deleteDivisionAction(Request $request)
    {
        $id = $request->sum_desc_id;
        $clients = DB::table('tbl_summary_description')
            ->where('sum_desc_id',$id)
            ->first();
        
        if($clients)
        {
            $sum_desc_id = $clients->sum_desc_id;
            DB::table('tbl_summary_description')->where('sum_desc_id',$id)->update(['sum_desc_status'=>1]);
            $ip = $request->ip();
            $action = '';
            $user_name = Auth::user()->name;
            $user_id = Auth::user()->id;
            $category = "Delete Summary Description";
            $activity = 'Summary Description No'.strip_tags($sum_desc_id).' Has been Deleted By '.$user_name.' ';
            $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
            Core::userActivityAction($log_array);
       
            return response()->json(['status' => 1, 'msg' => 'Summary Description Deleted Successfully!', 'heading' => 'Success']);
        }
        else
        {
            return response()->json(['heading'=>'Error','text'=>'Summary Description not found','icon'=>'error']);
        }
    }
}