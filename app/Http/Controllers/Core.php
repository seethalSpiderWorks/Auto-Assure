<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Activity;
use App\Activity1;
use Modules\Enquery\Models\EnqueryModel;
use Illuminate\Support\Facades\Hash;
use Modules\Branches\Models\BranchesModel;
use Hashids;
use Modules\Users\Models\UsersModel;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class Core extends Controller
{
    public static function userActivityAction($data){
        //Logs & Activity Manager.
      Activity::create($data);
    }
    
    public static function userActivityAction1($data){
        //Logs & Activity Manager.
      Activity1::create($data);
    }
    
    /**
     * @function decodeId
     * 
     * this function will decode id
     * 
     * @Param decoded id
     *
     * @Return encoded data
     */
    public static function decodeId($decodedId) {
        if (!empty($decodedId)) {
            $decodedId = Hashids::decode($decodedId);
            if (count($decodedId) > 0) {
                return $decodedId[0];
            }
        }
    }
    
    public static function getTodayEnqueryCount($id)
    {
        $userId = Auth::user()->id;
        $previlage = Auth::user()->previlage;
        $today = Carbon::now()->format('Y-m-d');
        $branchOldId = BranchesModel::select('old_id')->where('id',$id)->first();
        $branchOldId = $branchOldId['old_id'] ;
        if($previlage =='4' || $previlage =='1'||$previlage =='2' || $previlage =='19')
       {
        $todayEnqCount = EnqueryModel::select('enq_id')->where('tbl_enquiry.enq_date','=',$today)
                            ->leftjoin('branches',function($join) 
                        {
                            $join->on('branches.old_id','tbl_enquiry.centre_id')->where(['tbl_enquiry.enq_source'=>'WEBSITE']);
                            $join->orOn('branches.id','tbl_enquiry.centre_id')->where('tbl_enquiry.enq_source','!=','WEBSITE');
                            
                        })
                        ->where(function($query) use ($branchOldId,$id) {
                        $query->where('tbl_enquiry.centre_id','=',$branchOldId)->where(['tbl_enquiry.enq_source'=>'WEBSITE']);
                        $query->orWhere('tbl_enquiry.centre_id','=',$id)->where('tbl_enquiry.enq_source','!=','WEBSITE');
                      }) ->count();
       }else{
           $todayEnqCount = EnqueryModel::select('enq_id')->where('tbl_enquiry.enq_date','=',$today)
                            ->leftjoin('branches',function($join) 
                        {
                            $join->on('branches.old_id','tbl_enquiry.centre_id')->where(['tbl_enquiry.enq_source'=>'WEBSITE']);
                            $join->orOn('branches.id','tbl_enquiry.centre_id')->where('tbl_enquiry.enq_source','!=','WEBSITE');
                            
                        })->where('tbl_enquiry.enq_guide_id',$userId)
                        ->where(function($query) use ($branchOldId,$id) {
                        $query->where('tbl_enquiry.centre_id','=',$branchOldId)->where(['tbl_enquiry.enq_source'=>'WEBSITE']);
                        $query->orWhere('tbl_enquiry.centre_id','=',$id)->where('tbl_enquiry.enq_source','!=','WEBSITE');
                      }) ->count();
       }
        
       
        return $todayEnqCount ;
    }
     public static function getEnqueryCount($id)
    {
        $userId = Auth::user()->id;
        $previlage = Auth::user()->previlage;
        $branchOldId = BranchesModel::select('old_id')->where('id',$id)->first();
        $branchOldId = $branchOldId['old_id'] ;
        if($previlage =='4' || $previlage =='1'||$previlage =='2' || $previlage =='19')
       {
         $enqCount = EnqueryModel::select('enq_id')
                    ->leftjoin('branches',function($join) 
                        {
                            $join->on('branches.old_id','tbl_enquiry.centre_id')->where(['tbl_enquiry.enq_source'=>'WEBSITE']);
                            $join->orOn('branches.id','tbl_enquiry.centre_id')->where('tbl_enquiry.enq_source','!=','WEBSITE');
                            
                        })
                        ->where(function($query) use ($branchOldId,$id) {
                        $query->where('tbl_enquiry.centre_id','=',$branchOldId)->where(['tbl_enquiry.enq_source'=>'WEBSITE']);
                        $query->orWhere('tbl_enquiry.centre_id','=',$id)->where('tbl_enquiry.enq_source','!=','WEBSITE');
                      }) ->count();
         
       }else{
            $enqCount = EnqueryModel::select('enq_id')
                    ->leftjoin('branches',function($join) 
                        {
                            $join->on('branches.old_id','tbl_enquiry.centre_id')->where(['tbl_enquiry.enq_source'=>'WEBSITE']);
                            $join->orOn('branches.id','tbl_enquiry.centre_id')->where('tbl_enquiry.enq_source','!=','WEBSITE');
                            
                        })->where('tbl_enquiry.enq_guide_id',$userId)
                        ->where(function($query) use ($branchOldId,$id) {
                        $query->where('tbl_enquiry.centre_id','=',$branchOldId)->where(['tbl_enquiry.enq_source'=>'WEBSITE']);
                        $query->orWhere('tbl_enquiry.centre_id','=',$id)->where('tbl_enquiry.enq_source','!=','WEBSITE');
                      }) ->count();
       }
        return $enqCount ;
    }
    
    
    public static function getFollowupCountAction($id)
    {
        $userId = Auth::user()->id;
        $previlage = Auth::user()->previlage;
        $branchOldId = BranchesModel::select('old_id')->where('id',$id)->first();
        $branchOldId = $branchOldId['old_id'] ;
        $today = Carbon::now()->format('Y-m-d');
        if($previlage =='4' || $previlage =='1'||$previlage =='2' || $previlage =='19')
       {
          $followCount= EnqueryModel::select('tbl_enquiry.enq_id','tbl_enquiry.reference_no','tbl_followup.follow_id','tbl_followup.follow_comments','tbl_enquiry.enq_mobileno','tbl_followup.follow_next_date','divisions.division_name','branches.branch_name','tbl_enquiry.enq_source','tbl_enquiry.enq_firstname','tbl_enquiry.enq_lastname','tbl_new_courses.course_name','tbl_enquiry.enq_status')
                    ->leftjoin('tbl_new_courses','tbl_new_courses.course_id','tbl_enquiry.enq_course_id')
                   ->leftjoin('branches','branches.id','tbl_enquiry.centre_id')
                   ->join('tbl_followup','tbl_followup.follow_enq_id','tbl_enquiry.enq_id')
                    ->leftjoin('divisions','divisions.id','branches.division_id')
                    ->where('tbl_enquiry.centre_id','=',$id)
                   ->whereRaw("follow_id IN (select MAX(follow_id) FROM tbl_followup  WHERE follow_next_date ='$today' GROUP BY follow_enq_id)")
                   ->where('tbl_followup.status','!=','Joined')
                    ->where('tbl_followup.active_status','=','0')->count();
         
       }else{
           $followCount= EnqueryModel::select('tbl_enquiry.enq_id','tbl_enquiry.reference_no','tbl_followup.follow_id','tbl_followup.follow_comments','tbl_enquiry.enq_mobileno','tbl_followup.follow_next_date','divisions.division_name','branches.branch_name','tbl_enquiry.enq_source','tbl_enquiry.enq_firstname','tbl_enquiry.enq_lastname','tbl_new_courses.course_name','tbl_enquiry.enq_status')
                    ->leftjoin('tbl_new_courses','tbl_new_courses.course_id','tbl_enquiry.enq_course_id')
                   ->leftjoin('branches','branches.id','tbl_enquiry.centre_id')
                   ->join('tbl_followup','tbl_followup.follow_enq_id','tbl_enquiry.enq_id')
                    ->leftjoin('divisions','divisions.id','branches.division_id')
                    ->where('tbl_enquiry.centre_id','=',$id)
                   ->whereRaw("follow_id IN (select MAX(follow_id) FROM tbl_followup  WHERE follow_next_date ='$today'  GROUP BY follow_enq_id)")
                   ->where('tbl_followup.status','!=','Joined')
                   ->where('tbl_enquiry.enq_guide_id',$userId)
                   ->where('tbl_followup.active_status','=','0')->count();
       }
        return $followCount ;
    }
	
	
 public function resetMyPassword(Request $request){
     	  $userId = $request->id;
		  $user_mypass_current = strip_tags($request->user_mypass_current);
		  $user_mypass_new = strip_tags($request->user_mypass_new);
		  $user_mypass_conf = strip_tags($request->user_mypass_conf);
		
		$old_password=Auth::user()->password;
		if (Hash::check($user_mypass_current, $old_password)) {
 $allow = 1; 
}else{
	$allow =0;
}
		
		
		$data = array(
    		'password'=>hash::make($user_mypass_new)    		
    	);
     	
		if(($user_mypass_new == $user_mypass_conf) && ($user_mypass_new != '' && ($allow ==1))){
		$input = UsersModel::where(['user_id'=>$userId])->update($data);
     	if($input){
            //Logs & Activity Manager.
                $ip=$request->ip();
                $action = '';
                $user_name=Auth::user()->name;
                $user_id = Auth::user()->id;
                $activity = 'User  Has been Deleted By '. $user_name.' ';
                $log_array=array('activity_ip'=>$ip,'activity_action'=>$action,'activity_user'=>$user_name,'activity_user_id'=>$user_id,'activity_desc'=>$activity);  
                Core::userActivityAction($log_array);
     			return response()->json(['status' => 1, 'msg' => 'Password Reset successfully!', 'heading' => 'Success']);
     	}
		}else{
			return response()->json(['status' => 0, 'msg' => 'Password Does Not match!', 'heading' => 'Warning']);
		}

     }	
	
	public static function sendEmail($send = [], $mail = true) 
    {
        if ($mail) {
            
            $send['subject'] = (isset($send['subject']) && $send['subject'] != '') ? $send['subject'] : 'Daily Report';
            $send['from_email'] = 'info@ojoreviews.com' ;
            $send['from_name'] = 'CADD CENTRE';
            $send['to_name'] = (isset($send['to_name']) && $send['to_name'] != '') ? $send['to_name'] : '';
            $send['msg'] = (isset($send['msg']) && $send['msg'] != '') ? $send['msg'] : '';

            if (isset($send['to_email']) && $send['to_email'] != '') {

                $sendMail = Mail::send('emails.email',$send, function ($message) use ($send) {
                            $message->from($send['from_email'], $send['from_name']);
                            $message->to($send['to_email']);
                            $message->subject($send['subject']);
                            if (isset($send['cc_email']) && $send['cc_email'] != '') {
                                $message->cc($send['cc_email']);
                            }
                            if (isset($send['bcc_email']) && $send['bcc_email'] != '') {
                                $message->bcc($send['bcc_email']);
                            }
                        });
  
                return ['status' => 1, 'msg' => 'Message sent'];
               
            } else {
              
                return ['status' => 0, 'msg' => 'Message not sent'];
            }
        } else {
            //return view('companies.email');
        }
    }
}
