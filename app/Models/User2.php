<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Privilege\Entities\PrivilegeModel;
use Modules\Branch\Entities\BranchModel;
use Modules\Rules\Entities\RulesModel;
use Modules\Rules\Entities\RulesubModel;
use Modules\Rules\Entities\SetoptionsModel;
use Hashids;
use Illuminate\Support\Facades\Auth;
use Modules\Company\Entities\CompanyModel;
use DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
	
	public function allmenuData(){
			$allmenuData = RulesModel::select('badge_class','main_id as id', 'main_menuname', 'main_link', 'main_icon')->where('main_status', '0')->orderBy('menu_order')->with('SubMenus')->get();			
			return $allmenuData;

    }
	
	  public function menuData(){
	 $userprevilage = Auth::user()->previlage; 
	 $userPrivilagedata = PrivilegeModel::select('id', 'alloted_mainmenus', 'alloted_submenus')->where('status', '0')->where('id','=',$userprevilage)->orderBy('id')->first();
	 return $userPrivilagedata;
    }
	
	 public function menuRules($segment){
    	
	 $userprevilage = Auth::user()->previlage; 
	 $usermenudata = RulesubModel::select('sub_id', 'sub_main_id')->where('sub_status', '0')->where('sub_link','=',$segment)->orderBy('sub_id')->first();
	 return $usermenudata;
    }

	 public function menuoptionRules($segment){
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
		
		$result = array();
		$branchesData = BranchModel::select('tbl_branch.branch_id', 'branch_name')->join('tbl_company', 'tbl_company.company_id','=','tbl_branch.company_id')->where('tbl_branch.branch_status', '0')->orderBy('branch_name')->get()->toArray();
			
		$userprevilage = Auth::user()->previlage; 
		//dd($userprevilage);
		$userPrivilagedata = PrivilegeModel::select('id', 'privilege_name', 'privilege_code', 'alloted_divisions', 'alloted_branches')->where('status', '0')->where('id','=',$userprevilage)->orderBy('id')->first();
		//dd($userPrivilagedata);
		$branch_array = json_decode($userPrivilagedata['alloted_branches']);
		if(empty($branch_array)){ $branch_array = array(); }
	 
		$branch = Auth::user()->user_branch;
		if(session('branch'))
		{  
			$branch = session('branch'); 
			//$branch = session('application_branch'); 
		}
		else
		{ 
			session(['branch' =>$branch ]);
		}
		
		
		if(Auth::user()->user_multiple_branch == 1){
			$arrayData = explode(',', Auth::user()->user_multiple_branch_id);
			$branch_array = explode(',', Auth::user()->user_multiple_branch_id);
		}
		$userBranchsingledata = BranchModel::select('tbl_branch.branch_id', 'branch_name')->where('branch_status', '0')->where('branch_id','=',$branch)->orderBy('branch_id')->first();
	//dd($branch);
		foreach($branchesData as $val)
		{
				$branchId = $val['branch_id'];
				$branchName = $val['branch_name'];
				//print_r($branch_array);
				if(in_array($branchId,$branch_array))
				{ 
					//dd(1);
					$result[$branchId] = $branchName; 
				}
				else
				{ 
					//dd(2);
					$result[$branch] = $userBranchsingledata['branch_name']; 
				}
		}
			return $result; 
		}

}
