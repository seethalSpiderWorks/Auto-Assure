<?php

namespace Modules\Company\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
 
use Illuminate\Support\Str;
use DB;
use App\Http\Controllers\Core;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Modules\Company\Models\CompanyModel;
use Modules\Divisions\Models\DivisionsModel;

class CompanyController extends Controller
{
	public function indexAction()
	{
        $state = DB::table("states")
			->where("country_id",101)
			->get()->toArray();
        return view ('company::companyadd')->with(['state'=>$state]);
    }

    public function getAllDivisionAction(Request $request) 
	{
        $limit  = ($request->length != '') ? $request->length : 10;
        $offset = ($request->start != '') ? $request->start : 0;
        $search = $request->search['value'];
        $order  = $request->order;
        $columns = $request->columns;
        $colName = 'tbl_company.company_id';
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
         
        $division = CompanyModel::select('tbl_company.*','users.name','states.name as state_name','cities.name as city')
			->join('states', 'states.id','=','tbl_company.company_state')
			->join('cities', 'cities.id','=','tbl_company.company_district')
			->join('users', 'users.id','=','tbl_company.company_addedby')
			->Where(function($query) use ($search) {
				$query->where('tbl_company.company_name', 'like', $search . '%');
				$query->orWhere('tbl_company.company_shortcode', 'like', $search . '%');
				$query->orWhere('tbl_company.company_unqid', 'like', $search . '%');
				$query->orWhere('tbl_company.company_mob', 'like', $search . '%');
				$query->orWhere('tbl_company.company_email', 'like', $search . '%');
				$query->orWhere('tbl_company.company_state', 'like', $search . '%');
			  // $query->orWhere('tbl_company.company_email', 'like', $search . '%');
			});

        $division->where('tbl_company.company_status', '0');
        $division->orderBy('tbl_company.company_id', 'DESC');
        if ($colName != '' && $sort != '') 
		{
            $division->orderBy($colName, $sort);
        } 
        
        $data = ["iTotalDisplayRecords" => $division->count(), "iTotalRecords" => $division->count(), "TotalDisplayRecords" => $limit];
        $data['data'] = $division->skip($offset)->take($limit)->get()->toArray();

        return response()->json($data);
    }

    public function addCompanyAction(Request $request)
	{
        //$previlage = Auth::user()->previlage;
        
        $previlage = Auth::user()->id;
        if ($request->hasFile('company_logo')) 
		{
			$file = $request->file('company_logo'); //i need this later to upload the file
			$rules= [
				'company_logo' => "required"
			];

			$file_name = str::random(30) . '.' . $file->getClientOriginalExtension();
			$path = base_path() . '/public/uploads/company_logos';
			$file->move($path, $file_name);
			$fullpath = $file_name;
		}
		else
		{          
			$fullpath="";
		}

		$uid = DB::table('tbl_company')
			->select('company_unqid')
			->orderby('company_id', 'desc')
			->first();
			
		if($uid == "")
		{ 
			$uid = "C00000"; 
		}
		else
		{ 
			$uid = $uid->company_unqid;
		}
		$cnt = str_split($uid);
		$alph = $cnt[0];
		$num = $cnt[1].$cnt[2].$cnt[3].$cnt[4].$cnt[5];
		$final_num = $alph.$num;
		$num = $num+1;
		$unq_id = $alph.sprintf('%05d',$num);

		$data = array(
			'company_ip'=>$request->ip(),
			'company_addedby'=>$previlage,
			'company_unqid'=> $unq_id,
			'company_name'=>$request->company_name,
			'company_shortcode' =>$request->company_shortcode,
			'company_person' =>$request->company_person,
			'company_design' =>$request->company_design,
			'company_mob' =>$request->company_mob,
			'company_land' =>$request->company_land,
			'company_email' =>$request->company_email,
			'company_web' =>$request->company_web,
			'company_address' =>$request->company_address,
			'company_state' =>$request->company_state,
			'company_district' =>$request->company_district,

			'company_pin' =>$request->company_pin,
			'company_gstin' =>$request->company_gstin,
			'company_pan' =>$request->company_pan,
			'company_cin' =>$request->company_cin,
			'company_tds' =>$request->company_tds,
			'company_logo' =>$fullpath,
			'company_latitude' =>$request->company_latitude,
			'company_longitude' =>$request->company_longitude,
			'company_status'=>'0'
		);
		
		// if(!empty($request->company_land))
		// {
		//     $pattern = "/^[0-9]{11}+$/";
		//     if(!preg_match($pattern, $request->company_land))
		//     {
		//         return response()->json(['status' => 0, 'msg' => 'Land number should be 11 digits!', 'heading' => 'Warning']);
		//     }
		// }
    
		$validator = Validator::make($data,[
			'company_name'=>'required',
			'company_shortcode' =>'required',
			'company_address' =>'required',
			'company_state' =>'required',
			'company_district' =>'required',
			'company_pin' =>'regex:/^\d*\.?\d*$/',
			// 'company_gstin' =>'nullable|regex:/^\d*\.?\d*$/',
			// 'company_pan' =>'nullable|regex:/^\d*\.?\d*$/',
			// 'company_cin' =>'nullable|regex:/^\d*\.?\d*$/',
			// 'company_tds' => 'nullable|regex:/^\d*\.?\d*$/',
			// 'company_latitude' => 'nullable|regex:/^\d*\.?\d*$/',
			// 'company_longitude' => 'nullable|regex:/^\d*\.?\d*$/',
		]);
		
		if ($validator->fails()) 
		{
			foreach (array_values($validator->messages()->toArray()) as $msg) {
				$error = implode(' ', $msg) . '<br>';
			}
			return response()->json(['status' => 0, 'msg' => $error]);
		} 
		else 
		{
			$input = DB::table("tbl_company")->insert($data);
			//$accounts = new GenerateAccountsForNewCompany(5);
			// dump($accounts);die;
			if($input)
			{
				//Logs & Activity Manager.
				$ip = $request->ip() ;
				$action = '';
				$user_name=Auth::user()->name;
				$user_id = Auth::user()->id;
				$activity = 'New Company '.$data['company_name'].' Has been Added By '. $user_name.' ';
				$log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
				Core::userActivityAction($log_array);
				return response()->json(['status' => 1, 'msg' => 'Company added successfully!', 'heading' => 'Success']);
				try {

				} catch (\PDOException $e) {
					return Core::log(__CLASS__, __FUNCTION__, $e);
				} catch (\Exception $e) {
					return Core::log(__CLASS__, __FUNCTION__, $e);
				}
			}
		}
	}

	public function getEnqueryDataAction(Request $request)
	{
		$enqId   = $request->company_id;
		$enquery = CompanyModel::select('tbl_company.*','states.name as state_name','cities.name as city')
			->join('states', 'states.id','=','tbl_company.company_state')
			->join('cities', 'cities.id','=','tbl_company.company_district')
			->where(['company_id'=>$enqId])->first();

		return response()->json(['company_status' => 1,'data'=>$enquery]);            
	}

	public function getDivisionAction(Request $request)
	{
		$divisionId =$request->company_id;
		$data  = CompanyModel::select('tbl_company.*','tbl_company.company_id as id')
			->where('tbl_company.company_id',$divisionId)
			->first();

		return response()->json(['company_status' => 0,'data'=>$data]);
	}	


	public function editDivisionAction(Request $request)
	{
		$DivisionId = $request->company_id;

		$data = array(
            'company_name'=>$request->company_name,
            'company_shortcode' =>$request->company_shortcode,
            'company_person' =>$request->company_person,
            'company_design' =>$request->company_design,
            'company_mob' =>$request->company_mob,
            'company_land' =>$request->company_land,
            'company_email' =>$request->company_email,
            'company_web' =>$request->company_web,
            'company_address' =>$request->company_address,
            'company_state' =>$request->company_state,
            'company_district' =>$request->company_district,

            'company_pin' =>$request->company_pin,
           // 'company_gstin' =>$request->company_gstin,
          //  'company_pan' =>$request->company_pan,
         //   'company_cin' =>$request->company_cin,
         //   'company_tds' =>$request->company_tds,
          //  'company_latitude' =>$request->company_latitude,
          //  'company_longitude' =>$request->company_longitude
        );
        
        if(!empty($request->company_land))
		{
			$pattern = "/^[0-9]{11}+$/";
			if(!preg_match($pattern, $request->company_land))
			{
				return response()->json(['status' => 0, 'msg' => 'Land number should be 11 digits!', 'heading' => 'Warning']);
			}
		}
        
		$validator = Validator::make($data,[
			// 'company_name'=>'required',
			// 'company_shortcode' =>'required',
			// 'company_address' =>'required',
			// 'company_state' =>'required',
			// 'company_district' =>'required',
			// 'company_pin' =>'numeric',
			// 'company_latitude' =>'regex:/^\d*\.?\d*$/',
			// 'company_longitude' =>'regex:/^\d*\.?\d*$/',
			// 'company_mob' =>'numeric|digits:10',
			// 'company_land' =>'numeric|digits:11'
			
			
			'company_name'=>'required',
			'company_shortcode' =>'required',
			'company_address' =>'required',
			'company_state' =>'required',
			'company_district' =>'required',
			'company_pin' =>'regex:/^\d*\.?\d*$/',
			'company_gstin' =>'nullable|regex:/^\d*\.?\d*$/',
			'company_pan' =>'nullable|regex:/^\d*\.?\d*$/',
			'company_cin' =>'nullable|regex:/^\d*\.?\d*$/',
			'company_tds' => 'nullable|regex:/^\d*\.?\d*$/',
			'company_latitude' => 'nullable|regex:/^\d*\.?\d*$/',
			'company_longitude' => 'nullable|regex:/^\d*\.?\d*$/',
		]);
		
		if ($validator->fails()) 
		{
			foreach (array_values($validator->messages()->toArray()) as $msg) {
				$error = implode(' ', $msg) . '<br>';
			}
			return response()->json(['status' => 0, 'msg' => $error]);
		} 
		else 
		{
    
			$company_logo =$request->company_logo;
			$file = $request->file('company_logo'); 
			if($file)
			{
				$file_name = str::random(30) . '.' . $file->getClientOriginalExtension();
				$path = base_path() . '/public/uploads/company_logos';
				$file->move($path, $file_name);
				$fullpath = $file_name;
				$data['company_logo'] = $fullpath;
		   }
       
			$input = CompanyModel::where(['company_id'=>$DivisionId])->update($data);
		}

       if($input)
	   {
            //Logs & Activity Manager.
			$ip=$request->ip();
			$action = '';
			$user_name=Auth::user()->name;
			$user_id = Auth::user()->id;
			$activity = 'Company '.$data['company_name'].' Has been Edited By '. $user_name.' ';
			$log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
			Core::userActivityAction($log_array);
			return response()->json(['status' => 1, 'msg' => 'Company updated successfully!', 'heading' => 'Success']);
			try {

			} catch (\PDOException $e) {
				return Core::log(__CLASS__, __FUNCTION__, $e);
			} catch (\Exception $e) {
				return Core::log(__CLASS__, __FUNCTION__, $e);
			}
		}
	}

public function deleteDivisionAction(Request $request){
    $divisionId = $request->company_id;
    $data = array(
        'company_status'=>strip_tags('1')    		
    );
    $input = CompanyModel::where(['company_id'=>$divisionId])->update($data);
    if($input){
            //Logs & Activity Manager.
        $ip=$request->ip();
        $action = '';
        $user_name=Auth::user()->name;
        $user_id = Auth::user()->id;
        $activity = 'Company  Has been Deleted By '. $user_name.' ';
        $log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
        Core::userActivityAction($log_array);
        return response()->json(['status' => 1, 'msg' => 'Deleted successfully!', 'heading' => 'Success']);
    }

}	
public function get_city(Request $request)
{
    $cities = DB::table("cities")
    ->where("state_id",$request->id)
    ->pluck("name","id");
    return response()->json($cities);
}
##################Product Checking######################
public function Search_product (Request $request){

      ini_set('memory_limit', '1024M'); // or you could use 1G

      $search     = $request->q;
      $products = DB::table('tbl_products')
                ->where('product_status', 0)
                ->join('tbl_stock', 'tbl_stock.stock_item_id','=','tbl_products.product_id')
                // ->where('tbl_stock.stock_branch',session('application_branch'))
                        ->where(function($query) use ($search) {
                    $query->orWhere('tbl_products.product_name_en', 'like',"%". $search . '%');
                    $query->orWhere('tbl_products.product_sku_autogen', 'like', $search . '%');
                    $query->orWhere('tbl_products.sku_old', 'like', $search . '%');

                });
              $products= $products->groupBy('tbl_products.product_id')
                ->take(10)
                ->get();
                
       if(!empty($products))
        {
            $data['response'] = 'true';
            foreach($products as $result1)
            {
                $data['message'][] = array('value'=>$result1->product_name_en,'price'=>$result1->two_two_offer_price,'stock'=>$result1->stock_item_stock,'product_id'=>$result1->product_id);

            }
        }
        else
        {
            $data['response'] = 'false';
        }         
       
      return response()->json($data);          
    



}
public function Search_product_each_branch_stock(Request $request){
  $product_id=$request->product_id;
  
//   dd($product_id);
  
  $fetch_branch=DB::table('tbl_branch')->select('branch_id','branch_name')->where('branch_status',0)->get();
  $branch_stock_array=array();
  $i=0;
  foreach($fetch_branch as $branches)
  {
      $branch_id=$branches->branch_id;
      $branch_name=$branches->branch_name;
      $fetch_current_product_stock=DB::table('tbl_stock')
      ->select('stock_item_stock')
      ->where('stock_item_id',$product_id)
      ->where('stock_branch',$branch_id)
      ->where('stock_item_stock','>',0)
      ->where('stock_status',0)
      ->limit(1)
      ->orderBy('stock_id','DESC')
      ->first();
      
      
    //   dd($fetch_current_product_stock);
      
      
     if($fetch_current_product_stock==""){$stock=0;}
     else{
     $stock=$fetch_current_product_stock->stock_item_stock;
     $branch_stock_array[$i]['branch_name']=$branch_name;
     $branch_stock_array[$i]['Stock_item_stock']=$stock;
     $branch_stock_array[$i]['branch_id']=$branch_id;
       $i++;
     }


    
  }
  return $branch_stock_array;
}
}
