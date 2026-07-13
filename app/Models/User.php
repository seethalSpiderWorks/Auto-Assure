<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Modules\Divisions\Models\DivisionsModel;
use Modules\Privilege\Models\PrivilegeModel;
use Modules\Branches\Models\BranchesModel;
use Modules\Branch\Models\BranchModel;
use Modules\Rules\Models\RulesModel;
use Modules\Rules\Models\RulesubModel;
use Modules\Rules\Models\SetoptionsModel;
use Hashids;
use Illuminate\Support\Facades\Auth;
use Modules\Company\Entities\CompanyModel;
use DB;
class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getEmpIdAttribute($id) {
        if (!empty($id)) {
            return Hashids::encode($id);
        }
    }

    /**
     * Privilege id for "Technician User" (privilege_code = TECH).
     */
    public const TECHNICIAN_PRIVILEGE = 49;

    /**
     * Technicians use the mobile app/API only, never the CRM web UI.
     */
    public function isTechnician(): bool
    {
        return (int) $this->previlage === self::TECHNICIAN_PRIVILEGE;
    }

    public function dashboardAction()
    {
	    $divisionData  = DivisionsModel::select('id', 'division_name')->where('status', '0')->orderBy('division_name')->get()->toArray();
	    $userprevilage = Auth::user()->previlage; 
	    $userPrivilagedata = PrivilegeModel::select('id', 'privilege_name', 'privilege_code', 'alloted_divisions', 'alloted_branches')->where('status', '0')->where('id','=',$userprevilage)->orderBy('id')->first();

	    $division_array = json_decode($userPrivilagedata['alloted_divisions']);
	  
	    if(empty($division_array)){ $division_array = array(); }
	
	    $division = Auth::user()->user_division;

	    $userDivisionsingledata = DivisionsModel::select('id', 'division_name')->where('status', '0')->where('id','=',$division)->orderBy('id')->first();

    	foreach($divisionData as $val){
           	$divisionId= $val['id'];
           	$divisionName = $val['division_name'];	
        	if(in_array($divisionId,$division_array)){$result[$divisionId] = $divisionName; }
        	else{ $result[$division] = $userDivisionsingledata['division_name']; }
    	}
	
	    return $result;
    }

	public function dashboardbranchAction()
	{
    	$division = Auth::user()->user_division;
    	if(session('division')){ $division = session('division'); }else{session(['division' =>$division ]); }
    	$result = array();
    	$branchesData = BranchesModel::select('branches.id', 'branch_name')->join('divisions', 'divisions.id','=','branches.division_id')->where('branches.status', '0')->where('division_id', $division)->orderBy('branch_name')->get()->toArray();
    	
    	 $userprevilage = Auth::user()->previlage; 
    	 $userPrivilagedata = PrivilegeModel::select('id', 'privilege_name', 'privilege_code', 'alloted_divisions', 'alloted_branches')->where('status', '0')->where('id','=',$userprevilage)->orderBy('id')->first();
    	 $branch_array = json_decode($userPrivilagedata['alloted_branches']);
    	 if(empty($branch_array)){ $branch_array = array(); }
	 
    	 $branch = Auth::user()->user_branch;
    	 if(session('branch')){  $branch = session('branch'); }else{ session(['branch' =>$branch ]);}
    	 $userBranchsingledata = BranchesModel::select('branches.id', 'branch_name')->where('status', '0')->where('id','=',$branch)->orderBy('id')->first();
	
    	foreach($branchesData as $val){
           	$branchId= $val['id'];
           	$branchName = $val['branch_name'];
           	if(in_array($branchId,$branch_array)){ $result[$branchId] = $branchName; }else{ $result[$branch] = $userBranchsingledata['branch_name']; }
        }
        return $result; 
    }
        
	public function branchesselectAction()
	{
	    $branchesData = BranchesModel::select('branches.id', 'branch_name', 'division_name')->join('divisions', 'divisions.id','=','branches.division_id')->where('branches.status', '0')->orderBy('branch_name')->get()->toArray();

    	foreach($branchesData as $val){
           	$branchId= $val['id'];
           	$branchName = $val['division_name']." - ".$val['branch_name'];
           	$result[$branchId] = $branchName;
        }
	    return $result;
    }

    public function branchesselectprivilegeAction()
    {
	    $branchesData = BranchesModel::select('branches.id', 'branch_name', 'division_name')->join('divisions', 'divisions.id','=','branches.division_id')->where('branches.status', '0')->orderBy('branch_name')->get()->toArray();
	
	    $userprevilage      = Auth::user()->previlage; 
    	$userPrivilagedata  = PrivilegeModel::select('id', 'privilege_name', 'privilege_code', 'alloted_divisions', 'alloted_branches')->where('status', '0')->where('id','=',$userprevilage)->orderBy('id')->first();
	    $branch_array = json_decode($userPrivilagedata['alloted_branches']);
	    if(empty($branch_array)){ 
	 	    $branch_array = array(); 
	    }
	
    	foreach($branchesData as $val){
    	   	$branchId   = $val['id'];
    	   	$branchName = $val['division_name']." - ".$val['branch_name'];
    	  	if(in_array($branchId,$branch_array)){ 
    	  		$result[$branchId] = $branchName; 
    	  	}	 
        }
	    return $result;
    }	

    public function branchesselectprivilegeAction1()
    {
	    $branchesData = BranchModel::select('tbl_branch.branch_id', 'branch_name')->where('tbl_branch.branch_status', '0')->orderBy('branch_name')->get()->toArray();
	
	    $userprevilage      = Auth::user()->previlage; 
	    $userPrivilagedata = PrivilegeModel::select('id', 'privilege_name', 'privilege_code', 'alloted_divisions', 'alloted_branches')->where('status', '0')->where('id','=',$userprevilage)->orderBy('id')->first();
	    $branch_array = json_decode($userPrivilagedata['alloted_branches']);
	    if(empty($branch_array)){ $branch_array = array(); }
	
    	foreach($branchesData as $val){
       	$branchId= $val['branch_id'];
       	$branchName = $val['branch_name'];
      	if(in_array($branchId,$branch_array)){ $result[$branchId] = $branchName; } 
        }
	    return $result;
    }	

    public function menuData(){
	    $userprevilage = Auth::user()->previlage; 
	    $userPrivilagedata = PrivilegeModel::select('id', 'alloted_mainmenus', 'alloted_submenus')->where('status', '0')->where('id','=',$userprevilage)->orderBy('id')->first();
	    return $userPrivilagedata;
    }

	public function allmenuData(){
		$allmenuData = RulesModel::select('badge_class','main_id as id', 'main_menuname', 'main_link', 'main_icon')->where('main_status', '0')->orderBy('menu_order')->with('SubMenus')->get();			
		return $allmenuData;
    }

    public function menuRules($segment){
	    $userprevilage = Auth::user()->previlage; 
	    $usermenudata = RulesubModel::select('sub_id', 'sub_main_id')->where('sub_status', '0')->where('sub_link','=',$segment)->orderBy('sub_id')->first();
	    return $usermenudata;
    }

    public function menuoptionRules($segment)
    {
	    $userprevilage = Auth::user()->previlage; 
	    $usermenudata = RulesubModel::select('sub_id', 'sub_main_id')->where('sub_status', '0')->where('sub_link','=',$segment)->orderBy('sub_id')->first();
	    if(empty($usermenudata)){
	        $usermenudata = RulesModel::select('main_id')->where('main_status', '0')->where('main_link','=',$segment)->orderBy('main_id')->first();
	        $sub_main_id = $usermenudata['main_id'];
	        $sub_id = '0';	 
	    }else{	 
	        $sub_main_id = $usermenudata['sub_main_id'];
	    $sub_id = $usermenudata['sub_id'];
	    }

	    $option_data = SetoptionsModel::select('*')
							   ->where('opset_status', '0')
							   ->where('opset_privilege','=',$userprevilage)
							   ->where('opset_main_id','=',$sub_main_id)
							   ->where('opset_sub_id','=',$sub_id)
							   ->orderBy('opset_id')->first();	
	    return $option_data['opset_options'];						   
    }	
	
	public function dashboardbranchAction_new()
	{
        $division = Auth::user()->user_division;
        if(session('division')){ $division = session('division'); }else{session(['division' =>$division ]); }
        $result = array();
        $branchesData = BranchModel::select('tbl_branch.branch_id', 'branch_name')->join('tbl_company', 'tbl_company.company_id','=','tbl_branch.company_id')->where('tbl_branch.branch_status', '0')->orderBy('branch_name')->get()->toArray();
    
        $userprevilage = Auth::user()->previlage; 
        $userPrivilagedata = PrivilegeModel::select('id', 'privilege_name', 'privilege_code', 'alloted_divisions', 'alloted_branches')->where('status', '0')->where('id','=',$userprevilage)->orderBy('id')->first();
        $branch_array = json_decode($userPrivilagedata['alloted_branches']);
        if(empty($branch_array)){ $branch_array = array(); }
	 
        $branch = Auth::user()->user_branch;
        if(session('branch'))
        {  
            $branch = session('branch'); 
            
        }
        else
        { 
            session(['branch' =>$branch ]);
        }
      
        $userBranchsingledata = BranchModel::select('tbl_branch.branch_id', 'branch_name')
            ->where('branch_status', '0')
            ->where('branch_id','=',$branch)
            ->orderBy('branch_id')
            ->first();   
     
    	foreach($branchesData as $val){
           	$branchId= $val['branch_id'];
           	$branchName = $val['branch_name'];
           	if(in_array($branchId,$branch_array))
           	{ 
           	    $result[$branchId] = $branchName; 
           	    
           	}
           	else
           	{ 
           	    $result[$branch] = $userBranchsingledata['branch_name']; 
           	}
        }

        return $result; 
    }
}
