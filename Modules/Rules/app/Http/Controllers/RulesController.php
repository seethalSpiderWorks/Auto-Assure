<?php

namespace Modules\Rules\Http\Controllers;
 
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Modules\Privilege\Models\PrivilegeModel;
use Modules\Branch\Models\BranchModel;
use Modules\Divisions\Models\DivisionsModel;
use Modules\Rules\Models\RulesModel;
use Modules\Rules\Models\OptionsModel;
use Modules\Rules\Models\RulesubModel;
use Modules\Rules\Models\SetoptionsModel;
use App\Http\Controllers\Core;

class RulesController extends Controller
{
    public function indexAction()
    {		 
	    $privilegeData = PrivilegeModel::select('id', 'privilege_name')
				->where('status', '0')->where('id','!=', '2')
				->orderBy('id')
				->get()->toArray();
		
	    foreach($privilegeData as $val)
	    {
   	        $privilegeId= $val['id'];
   	        $privilegeName = $val['privilege_name'];
   	        $result[$privilegeId] = $privilegeName;
        }	
    
	    return view ('rules::rulesList')->with(['privilegeData'=>$result]);
    }
	
    public function getAllrulesAction(Request $request) 
    {
        $privilege_id  = $request->privilege_name;
		$userprivilegedata = PrivilegeModel::select('id', 'privilege_name', 'alloted_mainmenus', 'alloted_submenus')->where('status', '0')->where('id','=',$privilege_id)->orderBy('id')->first();
		$alldata = RulesModel::select('main_id as id', 'main_menuname')->where('main_status', '0')->orderBy('main_menuname')->with('SubMenus')->get();	
		$optionsdata = OptionsModel::select('option_id as id', 'option_name')->where('option_status', '0')->orderBy('id')->get();	
		$optionchoosendata = SetoptionsModel::select('opset_id as id', 'opset_privilege', 'opset_main_id', 'opset_sub_id', 'opset_options')
							->where('opset_privilege','=',$privilege_id)
							->where('opset_status', '0')
							->orderBy('id')->get();	
        return view ('rules::rulesData')->with(['mainDisplay'=>$alldata,'optionsDisplay'=>$optionsdata,'optionsMenudata'=>$userprivilegedata,'choosen_privilege'=>$privilege_id,'optionchoosendata'=>$optionchoosendata]);
	}
			
    public function assignSubmenus(Request $request)
    {
        $privilege = $request->privilege;
		$submenus = $request->submenus;
		$mainmenusdata = $request->mainmenus;
		$submenu_array = json_decode($submenus);
		$mainmenusdata = json_decode($mainmenusdata);			
		$mainmenu_array = array();
		$mainmenus = RulesubModel::select('sub_main_id')->where('sub_status', '0')->whereIn('sub_id',$submenu_array)->groupBy('sub_main_id')->get()->toArray();	
		foreach($mainmenus as $mainmenu)
		{
			array_push($mainmenu_array,$mainmenu['sub_main_id']);;
		}
		foreach($mainmenusdata as $main)
		{				
			if(!in_array($main,$mainmenu_array)){ array_push($mainmenu_array,$main); }
		}
		
        // 	$data = array(
        //     		'alloted_mainmenus'=>json_encode($mainmenu_array),
        //     		'alloted_submenus' =>json_encode($submenu_array)
        //     	);		
        // 	$input = PrivilegeModel::where(['id'=>$privilege])->update($data);
        
		$branches_array = BranchModel::where('branch_status',0)->pluck('branch_id')->toJson();
		$data = array(
	    		    'alloted_mainmenus'=> json_encode($mainmenu_array),
	    		    'alloted_submenus' => json_encode($submenu_array),
					'alloted_branches' =>'',
	    		    //'alloted_branches' => $branches_array,
	        );		

		$input = PrivilegeModel::where(['id'=>$privilege])->update($data);		
    }

    public function assignOptions(Request $request)
    {
		$optionpriv_id = $request->optionpriv_id;
		$optionmain_id = $request->optionmain_id;
		$optionsub_id = $request->optionsub_id;
		$option_id = $request->option_id;
        $option_data = SetoptionsModel::select('*')
		               ->where('opset_status', '0')
		               ->where('opset_privilege','=',$optionpriv_id)
		               ->where('opset_main_id','=',$optionmain_id)
		               ->where('opset_sub_id','=',$optionsub_id)
					   ->orderBy('opset_id')->first();
		
        if($option_data) 
		{ 
		    $op_array = json_decode($option_data['opset_options']);
		    if(!in_array($option_id,$op_array)) 
		    { 
		        array_push($op_array,$option_id); 
		    }
		    else
		    {  
		        if (($key = array_search($option_id, $op_array)) !== false) 
		        { 
		            unset($op_array[$key]); 
		            $op_array = array_values($op_array);
		        }
		    }	
		    
		    $data = array('opset_options'=>json_encode($op_array));	
    	        
		    $input = SetoptionsModel::where(['opset_privilege'=>$optionpriv_id,'opset_main_id'=>$optionmain_id,'opset_sub_id'=>$optionsub_id])->update($data);
		}
		else
		{ 
		    $op_array = array();
		    array_push($op_array,$option_id);
		    $data = array(
    	 	        'opset_options'=>json_encode($op_array),    		
    	 	        'opset_privilege'=>$optionpriv_id,    		    		    		
    	 	        'opset_main_id'=>$optionmain_id,    		    		    		
    	 	        'opset_sub_id'=>$optionsub_id,
    	 	        'opset_status'=>0    
    	        );	 
		    $input = SetoptionsModel::create($data);
		} 
    }	

	public function assignallOptions(Request $request)
	{
        $optionpriv_id = $request->selall_pri_id;
		$option_id = $request->op_id;
		$check_status = $request->check_status;
		$submenus = $request->submenus;
		$mainmenus = $request->mainmenus;
	    $submenu_array = json_decode($submenus);			
	    $mainmenu_array = json_decode($mainmenus);
		    foreach($mainmenu_array as $mainmenu)
		    {
				$optionmain_id = $mainmenu;
				$option_data = SetoptionsModel::select('*')
							   ->where('opset_status', '0')
							   ->where('opset_privilege','=',$optionpriv_id)
							   ->where('opset_main_id','=',$optionmain_id)
							   ->where('opset_sub_id','=','0')
							   ->orderBy('opset_id')->first();	
							   
				if($option_data) 
				{						 
					$op_array = json_decode($option_data['opset_options']);
					if(!in_array($option_id,$op_array)) 
					{ 
				        array_push($op_array,$option_id); 
					}
					else
					{  
					    if (($key = array_search($option_id, $op_array)) !== false) 
						{ 
					        if($check_status == 'false')
						    {  
				                unset($op_array[$key]); 
						    }
						    $op_array = array_values($op_array);
					    }
					}	
						 
					$data = array('opset_options'=>json_encode($op_array));	
						    
					$input = SetoptionsModel::where(['opset_privilege'=>$optionpriv_id,'opset_main_id'=>$optionmain_id,'opset_sub_id'=>'0'])->update($data);
				}
				else
				{ 		
					$op_array = array();
					array_push($op_array,$option_id);
					$data = array(
					        'opset_options'=>json_encode($op_array),    		
						    'opset_privilege'=>$optionpriv_id,    		    		    		
						    'opset_main_id'=>$optionmain_id,    		    		    		
							'opset_sub_id'=>'0',
						    'opset_status'=>'0'
						);	 
					$input = SetoptionsModel::create($data);
				}
			}	
			
		    foreach($submenu_array as $optionsub_id)
		    {
				$MainData = RulesubModel::select('sub_id', 'sub_main_id')->where('sub_status', '0')->where('sub_id','=',$optionsub_id)->orderBy('sub_id')->first();
				$optionmain_id = $MainData['sub_main_id'];
				$option_data = SetoptionsModel::select('*')
							   ->where('opset_status', '0')
							   ->where('opset_privilege','=',$optionpriv_id)
							   ->where('opset_main_id','=',$optionmain_id)
							   ->where('opset_sub_id','=',$optionsub_id)
							   ->orderBy('opset_id')->first();				
				if($option_data) 
				{						 
					$op_array = json_decode($option_data['opset_options']);
					if(!in_array($option_id,$op_array)) 
					{ 
					    array_push($op_array,$option_id); 
					}
					else
					{  
					    if (($key = array_search($option_id, $op_array)) !== false) 
					    { 
						    if($check_status == 'false')
						    {  
						        unset($op_array[$key]); 
						    }
						    $op_array = array_values($op_array);
						 }
					}			 
					$data = array('opset_options'=>json_encode($op_array));	
						
					$input = SetoptionsModel::where(['opset_privilege'=>$optionpriv_id,'opset_main_id'=>$optionmain_id,'opset_sub_id'=>$optionsub_id])->update($data);
				}
				else
				{ 		
					$op_array = array();
					array_push($op_array,$option_id);
					$data = array(
							'opset_options'=>json_encode($op_array),    		
							'opset_privilege'=>$optionpriv_id,    		    		    		
							'opset_main_id'=>$optionmain_id,    		    		    		
							'opset_sub_id'=>$optionsub_id,
							'opset_status'=>'0'
						);	
						
					$input = SetoptionsModel::create($data);
				}
			}	
    }

	public function OptionCheck()
	{
	    return("abc");
	}
	
}
