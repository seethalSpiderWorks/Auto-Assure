<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class SearchController extends Controller
{
	public function customer_data(Request $request)
    {
		if($request->has('q')) 
        {
			$name = $request->q;

			$cdata = DB::table('tbl_basic_registration')
					->select('breg_id','breg_fname','breg_mob','breg_mob_code','breg_email','student_id')
					->leftjoin('tbl_student','tbl_student.student_reg_id','=','tbl_basic_registration.breg_id')
					//  ->leftjoin('tbl_lead_followup','tbl_lead_followup.followup_reg_id','breg_id')
					->where('breg_status',0)                    
					->where('breg_fname', 'LIKE', $name.'%')
					->orwhere('breg_mob', 'LIKE', $name.'%')
					->orwhere('breg_email', 'LIKE',$name.'%')
					->get();
			
			  return response()->json($cdata);
		}
		else
		{ 
		}
      
	}
	/*************************************************/
    public function customer_data_old(Request $request)
    {
        if ($request->has('q') && $request->has('flag')) 
        {
            if ($request->flag == 'get_single') 
            {
                $name1 = $request->q;
				
				$array = explode('-', $name1);
				$name  = trim($array[0]);
				
				$email  = $array[2];
			
			    $array1 = explode(' ', trim($array[1]));
			   
			    $mobile = trim($array1[1]);
				$email1 = trim($email);
				
                $cdata = DB::table('tbl_basic_registration')
                        ->select('breg_id','breg_fname','breg_mob','breg_mob_code','breg_email','student_id')
                        ->join('tbl_student','tbl_student.student_reg_id','=','tbl_basic_registration.breg_id')
                        ->where('breg_status',0)
                        ->where('breg_fname','=',$name)   
                        ->where('breg_mob',$mobile)
                        ->orWhere('breg_email','=',$email1)
                        ->first();
         
                return response()->json(['cdata'=>$cdata]);
            }
        } 
        else if($request->has('q')) 
        {
            $name = $request->q;
           
            $cdata = DB::table('tbl_basic_registration')
                         	->select('breg_id','breg_fname','breg_mob','breg_mob_code','breg_email','student_id')
                         	->leftjoin('tbl_student','tbl_student.student_reg_id','=','tbl_basic_registration.breg_id')
                        	//  ->leftjoin('tbl_lead_followup','tbl_lead_followup.followup_reg_id','breg_id')
                        	/*->leftjoin('tbl_lead_followup', function ($join) {
            						$join->on('tbl_lead_followup.followup_reg_id', '=', 'tbl_basic_registration.breg_id');
        						})*/
                        	->where('breg_status',0)                    
                        	->where('breg_fname', 'LIKE', $name.'%')
                        	->orwhere('breg_mob', 'LIKE', $name.'%')
                        	->orwhere('breg_email', 'LIKE',$name.'%')
                        	/*->orwhere('followup_remarks', 'LIKE', $name.'%') */
                        	->get();

            return response()->json($cdata);
        }
    }
}
