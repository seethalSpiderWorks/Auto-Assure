<?php

namespace Modules\Privilege\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Modules\Privilege\Models\PrivilegeModel;
use Modules\Divisions\Models\DivisionsModel;
use Modules\Company\Models\CompanyModel;
use Modules\Branches\Models\BranchesModel;
use Modules\Branch\Models\BranchModel;
use Modules\Privilege\Models\MenuModel;
use Modules\Privilege\Models\MenuPrivilege;
use App\Http\Controllers\Core;
use DB;

class PrivilegeController extends Controller
{
    public function indexAction()
	{		
		$divisionData = CompanyModel::select('company_id', 'company_name')
			->where('company_status', '0')
			->orderBy('company_name')
			->with('BranchesData')
			->get()->toArray(); 
		
		//dd($divisionData);
		/*
		$divisionData = CompanyModel::select('tbl_company.company_id', 'tbl_company.company_name','tbl_branch.branch_id','tbl_branch.branch_name')
                    ->join("tbl_branch","tbl_branch.company_id","tbl_company.company_id")
                    ->where('tbl_company.company_status', '0')
			         ->orderBy('tbl_company.company_name')->get()->toArray();
		$divisions = DB::table("tbl_branch")->select('branch_id','branch_name')->where('branch_status', '0')->groupBy('company_id')->get()->toArray();
		$divisionData['branches_data'] = $divisions;
//dd($divisionData);*/
  
		$privilagesData = DB::table("privilege")
			->select('id', 'privilege_name', 'privilege_code', 'alloted_divisions', 'alloted_branches')
			->where('status', '0')
			->where('id','!=','2')->orderBy('id','desc')->get()->toArray();
	//dd($privilagesData);
	return view ('privilege::privilegeList')->with(['divisionData'=>$divisionData,'privileges'=>$privilagesData]);
    }
	
	public function assignPrivilege(Request $request)
	{
		//dd(1);
		$privilege_id = strip_tags($request->priv_id);
		$branches = strip_tags($request->branches);

			$branch_array = json_decode($branches);
			$division_array = array();
			$divisions = BranchModel::select('company_id')->where('branch_status', '0')->whereIn('branch_id',$branch_array)->groupBy('company_id')->get()->toArray();	
			foreach($divisions as $division){
				array_push($division_array,$division['company_id']);;
			}
			
	$data = array(
    		'alloted_divisions'=>json_encode($division_array),
    		'alloted_branches' =>json_encode($branch_array)
    		
    	);		
		//dd($data);
	$input = PrivilegeModel::where(['id'=>$privilege_id])->update($data);		
			
    }

    /**
    function :getAllPrivilegeAction
    @parameter:
    @return :data
    **/
     public function getAllPrivilegeAction(Request $request) {
        $limit = ($request->length != '') ? $request->length : 10;
        $offset = ($request->start != '') ? $request->start : 0;
        $search = $request->search['value'];
        $order = $request->order;
        $columns = $request->columns;
        $colName = 'privilege.id';
        $sort = '';
        
        if (isset($order[0]['column']) && isset($order[0]['dir'])) {
            $colNo = $order[0]['column'];
            $sort = $order[0]['dir'];
            if (isset($columns[$colNo]['name'])) {
                $colName = $columns[$colNo]['name'];
            }
        }

        $privilege = PrivilegeModel::select('privilege.id','privilege.id as privilege_id','privilege.privilege_name','privilege.privilege_code')
                    ->Where(function($query) use ($search) {
                    $query->where('privilege.privilege_name', 'like', $search . '%');
                    $query->orWhere('privilege.privilege_code', 'like', $search . '%');
               });

		$privilege->where('status', '0');
		$privilege->where('id','!=','1');
		$privilege->orderBy('privilege.id', 'asc');
        if ($colName != '' && $sort != '') {
            $privilege->orderBy($colName, $sort);
        } 
        $data = ["iTotalDisplayRecords" => $privilege->count(), "iTotalRecords" => $privilege->count(), "TotalDisplayRecords" => $limit];
        $data['data'] = $privilege->skip($offset)->take($limit)->get()->toArray();
		$divisionData = DivisionsModel::select('id', 'division_name')->where('status', '0')->orderBy('division_name')->get()->toArray();
        
        return response()->json($data);
		}

		/**
    function :addCurrencyAction
    @parameter:
    @return :view
    **/

    public function addPrivilegeAction(Request $request){
    	$data = array(
    		'privilege_name'=>$request->privilege_name,
    		'privilege_code' =>$request->short_code,
    		'status'=>'0'
    	);
    	
    		$user = DB::table('privilege')
		->where('privilege_name', '=', $request->privilege_name)
		->Where('privilege_code', '=', $request->short_code)
		->where('status', '=', 0)
		->first();
    	  if ($user != null) {
           // user doesn't exist
           return response()->json(['status' => 0, 'msg' => 'Privilege Already Exists', 'heading' => 'Error']);
        }
    	
    	$validator = Validator::make($data,[
    		'privilege_name'=>'required',
    		'privilege_code' =>'required'

    	]);
    	if ($validator->fails()) {
            foreach (array_values($validator->messages()->toArray()) as $msg) {
                $error = implode(' ', $msg) . '<br>';
            }
            return response()->json(['status' => 0, 'msg' => $error]);
        } else {
        	$input = DB::table("privilege")->insert($data);
        	if($input){
                //Logs & Activity Manager.
                $ip=$request->ip();
                $action = '';
                $user_name=Auth::user()->name;
                $user_id = Auth::user()->id;
                $activity = 'New privilege '.$data['privilege_name'].' Has been Added By '. $user_name.' ';
                $log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
                Core::userActivityAction($log_array);
        		return response()->json(['status' => 1, 'msg' => 'Privilege added successfully!', 'heading' => 'Success']);
            try {
                
            } catch (\PDOException $e) {
                return Core::log(__CLASS__, __FUNCTION__, $e);
            } catch (\Exception $e) {
                return Core::log(__CLASS__, __FUNCTION__, $e);
            }
        	}
        }
    }
    /**
    function :getCurrencyAction
    @parameter:
    @return :view
    **/
    public function getPrivilegeAction(Request $request){
    	$privilegeId = $request->id;
    	$data  = PrivilegeModel::select('privilege.*','privilege.id as privilege_id')
    				->where('privilege.id',$privilegeId)
    				->first();
                    
     return response()->json(['status' => 0,'data'=>$data]);
    }
    /**
    function :editPrivilegeAction
    @parameter:
    @return :view
    **/
public function editPrivilegeAction(Request $request){

	//$privilegeId = Core::decodeId($request->privilege_id);
	$privilegeId = $request->privilege_id;

	$data = array(
    		'privilege_name'=>$request->privilege_name,
    		'privilege_code' =>$request->short_code
    		
    	);
    	$validator = Validator::make($data,[
    		'privilege_name'=>'required|unique:privilege,privilege_name,' . $privilegeId,
    		'privilege_code' =>'required'

    	]);
    	if ($validator->fails()) {
            foreach (array_values($validator->messages()->toArray()) as $msg) {
                $error = implode(' ', $msg) . '<br>';
            }
            return response()->json(['status' => 0, 'msg' => $error]);
        } else {
        	$input = PrivilegeModel::where(['id'=>$privilegeId])->update($data);
        	if($input){
                //Logs & Activity Manager.
                $ip=$request->ip();
                $action = '';
                $user_name=Auth::user()->name;
                $user_id = Auth::user()->id;
                $activity = 'Privilege '.$data['privilege_name'].' Has been Edited By '. $user_name.' ';
                $log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
                Core::userActivityAction($log_array);
        		return response()->json(['status' => 1, 'msg' => 'Privilege updated successfully!', 'heading' => 'Success']);
            try {
                
            } catch (\PDOException $e) {
                return Core::log(__CLASS__, __FUNCTION__, $e);
            } catch (\Exception $e) {
                return Core::log(__CLASS__, __FUNCTION__, $e);
            }
        	}
        }
    }
    /**
    function :deleteCurrencyAction
    @parameter:
    @return :view
    **/
     public function deletePrivilegeAction(Request $request){
     	$privilegeId = $request->id;
		$data = array(
    		'status'=>strip_tags('1')    		
    	);
     	$input = PrivilegeModel::where(['id'=>$privilegeId])->update($data);
     	if($input){
            //Logs & Activity Manager.
                $ip=$request->ip();
                $action = '';
                $user_name=Auth::user()->name;
                $user_id = Auth::user()->id;
                $activity = 'Privilege  Has been Deleted By '. $user_name.' ';
                $log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
                Core::userActivityAction($log_array);
     			return response()->json(['status' => 1, 'msg' => 'Deleted successfully!', 'heading' => 'Success']);
     	}

     }
      /**
    function :menuIndexAction
    @parameter:
    @return :view
    **/
     public function menuIndexAction(){
		return view ('privilege::menuPrivilege');

     }
     /**
    function :getAllPrivilege
    @parameter:
    @return :data
    **/
    public function getAllPrivilege(Request $request) {

        $search = $request->q;
         $privilegeData = PrivilegeModel::select('privilege.id', 'privilege.privilege_name')
                ->Where(function($query) use ($search) {
            $query->where('privilege_name', 'like', '%' . $search . '%');
        });
        
        $privilegeData->orderBy('privilege_name', 'asc')->get()->toArray();
        
        $data = array(
            "total_count" => PrivilegeModel::count(),
            "incomplete_results" => true,
            "items" => $privilegeData->get()->toArray()
        );

        return response()->json($data);
		}
  /**
    function :getMenuPrivilegePrivilege
    @parameter:
    @return :view
    **/
        public function getMenuPrivilege(Request $request){

            $privilegeId = $request->privilege_id;
            $result = MenuModel::select('tbl_menus.id as main_id', 'name', 'parent_id', 'display_name', 'order')
                         ->where('parent_id', 0)->orderBy('order')
                         ->with(['child_permissions' => function($sql) use ($privilegeId) {
                                $sql->select('tbl_menus.id as sub_id', 'name', 'parent_id', 'display_name', 'order', 'menu_id', 'privilege_id');
                                $sql->leftJoin('menu_privilege',function($join) use ($privilegeId)
                                    {
                                    $join->where('menu_privilege.privilege_id', '=', $privilegeId);
                                    $join->on('menu_privilege.menu_id', '=', 'tbl_menus.id');
                                                });
                                $sql->orderBy('order');
                                 }])->get()->toArray();

         $returnHTML = view('privilege::setPrivilege')->with(['permissions' => $result])->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));

        }
        /**
    function :updateMenuPrivilegeAction
    @parameter:
    @return :view
    **/
        public function updateMenuPrivilegeAction(Request $request){
            $data = array(
            'permissions' => $request->permissions,
            'privilege_id' => $request->privilege_id
        );

        $validator = Validator::make($data, [
                   'permissions.*' => 'required'
        ]);

        if ($validator->fails()) {

            foreach (array_values($validator->messages()->toArray()) as $msg) {
                $error = implode(' ', $msg) . '<br>';
            }
            return response()->json(['status' => 0, 'msg' => $error]);
        } else {
            try {
                $permissions = $data['permissions'];
                $permissionId = $data['privilege_id'];
               $data = MenuPrivilege::select()->where('privilege_id',$permissionId)->first();
               if($data){
                $delete =  MenuPrivilege::where(['privilege_id'=>$permissionId])->delete();
              }
              
                foreach ($permissions as $permission) {
                    MenuPrivilege::create(['menu_id' => $permission, 'privilege_id'=>$permissionId]);
                
            }
            //Logs & Activity Manager.
                $ip=$request->ip();
                $action = '';
                $user_name=Auth::user()->name;
                $user_id = Auth::user()->id;
                $activity = 'Menu privilege  Has been Added For privilege ID'. $permissionId.' By'.$user_name;
                $log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
                Core::userActivityAction($log_array);
                return response()->json(['status' => 1, 'msg' => 'success']);
            } catch (\PDOException $e) {
                return Core::log(__CLASS__, __FUNCTION__, $e);
            } catch (\Exception $e) {
                return Core::log(__CLASS__, __FUNCTION__, $e);
            }
        }

    }

}
