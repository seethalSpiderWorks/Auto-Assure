<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;

class HomeController extends Controller
{
  	public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }
	
	 public function version()
    {
        return view('version');
    }
	
    public function dashboardNew()
    {
		return view('home');
	}

	
	public function dashboard()
    {
        if(Auth::check())
        {
			$privilege = Auth::user()->previlage;
			$id = Auth::user()->id;
			 
			$centre = session('application_branch');
			
			DB::enableQueryLog();
			$lead = DB::table('tbl_lead')
				->select(DB::raw('count(lead_id) as count'), DB::raw('count(case when lead_followup_type = 1 then 1 else null end) as lead_assigned_count'), DB::raw('count(case when lead_followup_type = 4 then 1 else null end) as lead_student_count'))
				->where('lead_status',0);
					/*if($privilege != 2)
					{
						if($privilege == '1')
						{
						    $lead->where('lead_branch_id',$centre);
						}
						else
						{
							$lead->where('lead_branch_id',$centre);
							//$lead->where('lead_added_by',$id);
							//$lead->orWhere('lead_assigned_users',$id);
							$lead->where(function ($query) use ($id) {
							$query->where('lead_added_by', $id)
								->orWhere('lead_assigned_users', $id);
							});
						}
					}*/
					
			if($privilege == '48' && $privilege == '49')
			{
				$lead->where(function ($query) use ($id) 
				{
					$query->where('lead_added_by', $id)
						  ->orWhere('lead_assigned_users', $id);
						  //->orwhere('lead_branch_id',$centre);
				});
			}
			
			if ($centre == '38') {
                $lead->where(function ($query) use ($id, $centre) {
                    $query->where('lead_added_by', $id)
                          ->orWhere('lead_assigned_users', $id)
                          ->orWhere('lead_branch_id', $centre);
                });
            }
		 
			$lead = $lead->first();  
			
			$followup = DB::table('tbl_lead_followup')
					->select('followup_id')
					->where('followup_status',0);
			$followup->where('followup_branch_id',$centre);
			if($privilege == '48' && $privilege == '49')
			{
				$followup->where('followup_branch_id',$centre);
				$followup->where('followup_assigned_users_id',$id);
			}
			
			if ($centre == '38') {
			    $followup->where('followup_branch_id',$centre);
				$followup->where('followup_assigned_users_id',$id);
			}
			
			//$followup->groupBy('followup_lead_id');
			$followup = $followup->get()->count();
			
    	    //monthwise lead
    	    $Y=date('Y');
    		$TD = date('Y-m-d');
    
    		$YD = date('Y-01-01');
    		$YED = date('Y-12-31');
    
    		$M1 = date('F');
    		if(date("M")=="Mar" && date("d")>28) {  $M2="February";}
    		else{
    			$M2 = date('F', strtotime("-1 Months"));
    		}

    		$M3 = date('F', strtotime("-2 Months"));
    		$M4 = date('F', strtotime("-3 Months"));
    		$M5 = date('F', strtotime("-4 Months"));
    
    		$MD1 = date('Y-m-01');
    		if(date("M")=="Mar" && date("d")>28) {  $MD2="2024-02-01";}
    		else{
    			$MD2 = date('Y-m-01',strtotime("-1 Months"));
    		}
    		$MD3 = date('Y-m-01',strtotime("-2 Months"));
    		$MD4 = date('Y-m-01',strtotime("-3 Months"));
    		$MD5 = date('Y-m-01',strtotime("-4 Months"));
    
    		$MED1 = date('Y-m-t',strtotime($MD1));   
    		$MED2 = date('Y-m-t',strtotime($MD2));
    		$MED3 = date('Y-m-t',strtotime($MD3));
    		$MED4 = date('Y-m-t',strtotime($MD4));
    		$MED5 = date('Y-m-t',strtotime($MD5));
			
		    $data = DB::table('tbl_lead')
				->select(DB::raw('COUNT(CASE WHEN lead_date_on ="'.$TD.'" THEN 1 ELSE null END) countToday'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD1.'" AND lead_date_on <="'.$MED1.'" THEN 1 ELSE null END) countFM'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD2.'" AND lead_date_on <="'.$MED2.'" THEN 1 ELSE null END) countSM'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD3.'" AND lead_date_on <="'.$MED3.'" THEN 1 ELSE null END) countTM'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$YD.'" AND lead_date_on <="'.$YED.'" THEN 1 ELSE null END) countYR'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$TD.'" THEN 1 ELSE null END) as studentCountTdy'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD1.'" AND lead_date_on <="'.$MED1.'" THEN 1 ELSE null END) studentCountFM'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD2.'" AND lead_date_on <="'.$MED2.'" THEN 1 ELSE null END) studentCountSM'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD3.'" AND lead_date_on <="'.$MED3.'" THEN 1 ELSE null END) studentCountTM'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$YD.'" AND lead_date_on <="'.$YED.'" THEN 1 ELSE null END) studentCountYR')			 
			)
				->where('lead_status',0);
			//$data->where('lead_branch_id',$centre);
			if($privilege == 48)
			{
				$data->where('lead_branch_id',$centre);
				$data->where('lead_added_by',$id);
			}
			 
			$data = $data->first();
	 
			//DB::enableQueryLog();

			$fData = DB::table('tbl_lead_followup')
						->select(
					DB::raw('COUNT(CASE WHEN followup_date_on ="'.$TD.'" THEN 1 ELSE null END) countToday'),
					DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD1.'" AND followup_date_on <="'.$MED1.'" THEN 1 ELSE null END) countFM'),
					DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD2.'" AND followup_date_on <="'.$MED2.'" THEN 1 ELSE null END) countSM'),
					DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD3.'" AND followup_date_on <="'.$MED3.'" THEN 1 ELSE null END) countTM'),
					DB::raw('COUNT(CASE WHEN followup_date_on >="'.$YD.'" AND followup_date_on <="'.$YED.'" THEN 1 ELSE null END) countYR')
			            )
					->where('followup_status',0);
			
		    //	$fData->where('followup_branch_id',$centre);
			if($privilege == 48)
			{
				$fData->where('followup_branch_id',$centre);
				$fData->where('followup_assigned_users_id',$id);
			}
				 
				$fData = $fData->first();
			//dd(DB::getQueryLog());
			
		    //weekwise lead
			$dateArray=[];
			$currentTimestamp = time();
			$weekStartTimestamp = strtotime("last Sunday", $currentTimestamp);
			for ($i = 0; $i < 7; $i++) {
				$dayTimestamp = strtotime("+$i day", $weekStartTimestamp);
				$dateArray[$i] = date("Y-m-d", $dayTimestamp);	
			}
			
			
			$wData = DB::table('tbl_lead')
					->select(DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[0].'" THEN 1 ELSE null END) countSun'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[1].'" THEN 1 ELSE null END) countMon'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[2].'" THEN 1 ELSE null END) countTue'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[3].'" THEN 1 ELSE null END) countWed'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[4].'" THEN 1 ELSE null END) countThu'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[5].'" THEN 1 ELSE null END) countFri'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[6].'" THEN 1 ELSE null END) countSat'),
							 DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[0].'" THEN 1 ELSE null END) as studCountSun'),
							 DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[1].'" THEN 1 ELSE null END) as studCountMon'),
							  DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[2].'" THEN 1 ELSE null END) as studCountTue'),
							  DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[3].'" THEN 1 ELSE null END) as studCountWed'),
							 DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[4].'" THEN 1 ELSE null END) as studCountThu'),
							  DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[5].'" THEN 1 ELSE null END) as studCountFri'),
							  DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[6].'" THEN 1 ELSE null END) as studCountSat')
							)
					->where('lead_status',0);
			$wData->where('lead_branch_id',$centre);
			if($privilege == 48)
			{
				$wData->where('lead_branch_id',$centre);
				$wData->where('lead_added_by',$id);
			}

			$wData=$wData->first();

			$WFData='';

			// Inspections summary + latest inspections for the dashboard.
			// Technicians see only their own assigned inspections; CRM staff see all.
			$isTechnician = Auth::user()->isTechnician();

			$inspStats = \App\Models\Inspection::selectRaw("
					count(*) as total,
					sum(status = 'pending') as pending,
					sum(status = 'in_progress') as in_progress,
					sum(status = 'completed') as completed
				")
				->when($isTechnician, fn ($q) => $q->where('technician_id', $id))
				->first();

			$latestInspections = \App\Models\Inspection::with(['technician', 'lead'])
				->when($isTechnician, fn ($q) => $q->where('technician_id', $id))
				->latest()
				->take(8)
				->get();

			// Active (not yet completed) inspections assigned to the technician.
			$assignedInspections = $isTechnician
				? (int) \App\Models\Inspection::where('technician_id', $id)
					->whereIn('status', ['pending', 'in_progress'])
					->count()
				: 0;

            return view('dashboard',compact('lead','followup','data','fData','wData','WFData','inspStats','latestInspections','isTechnician','assignedInspections'));
        }
        
        return redirect('/')
            ->withErrors([
            'username' => 'Please login to access the dashboard.',
        ])->onlyInput('username');
    } 
	
	public function line_chart()
	{
			ini_set('memory_limit', '-1');
			$privilege = Auth::user()->previlage;
			$centre =  session('application_branch');
			$id = Auth::user()->id;

		    $dateArray=[];
			$currentTimestamp = time();
			$weekStartTimestamp = strtotime("last Sunday", $currentTimestamp);
			for ($i = 0; $i < 7; $i++) {
				$dayTimestamp = strtotime("+$i day", $weekStartTimestamp);
				$dateArray[$i] = date("Y-m-d", $dayTimestamp);	
			}
			
			$wData = DB::table('tbl_lead')
					->select(DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[0].'" THEN 1 ELSE null END) countSun'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[1].'" THEN 1 ELSE null END) countMon'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[2].'" THEN 1 ELSE null END) countTue'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[3].'" THEN 1 ELSE null END) countWed'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[4].'" THEN 1 ELSE null END) countThu'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[5].'" THEN 1 ELSE null END) countFri'),
							 DB::raw('COUNT(CASE WHEN lead_date_on ="'.$dateArray[6].'" THEN 1 ELSE null END) countSat'),
							 DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[0].'" THEN 1 ELSE null END) as studCountSun'),
							 DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[1].'" THEN 1 ELSE null END) as studCountMon'),
							  DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[2].'" THEN 1 ELSE null END) as studCountTue'),
							  DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[3].'" THEN 1 ELSE null END) as studCountWed'),
							 DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[4].'" THEN 1 ELSE null END) as studCountThu'),
							  DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on ="'.$dateArray[5].'" THEN 1 ELSE null END) as studCountFri'),
							  DB::raw('COUNT(CASE WHEN lead_followup_type = 11 AND lead_date_on ="'.$dateArray[6].'" THEN 1 ELSE null END) as studCountSat')
							)
					->where('lead_status',0);
			$wData->where('lead_branch_id',$centre);
			if($privilege == 48)
			{
				$wData->where('lead_branch_id',$centre);
				$wData->where('lead_added_by',$id);
			}
			$wData=$wData->first();
			
		$WFData = DB::table('tbl_lead_followup')
					->select([
							'followup_lead_id',
						DB::raw('COUNT(CASE WHEN followup_date_on = "'.$dateArray[0].'" THEN 1 ELSE null END) as countSun'),
						DB::raw('COUNT(CASE WHEN followup_date_on = "'.$dateArray[1].'" THEN 1 ELSE null END) as countMon'),
						DB::raw('COUNT(CASE WHEN followup_date_on = "'.$dateArray[2].'" THEN 1 ELSE null END) as countTue'),
						DB::raw('COUNT(CASE WHEN followup_date_on = "'.$dateArray[3].'" THEN 1 ELSE null END) as countWed'),
						DB::raw('COUNT(CASE WHEN followup_date_on = "'.$dateArray[4].'" THEN 1 ELSE null END) as countThu'),
						DB::raw('COUNT(CASE WHEN followup_date_on = "'.$dateArray[5].'" THEN 1 ELSE null END) as countFri'),
						DB::raw('COUNT(CASE WHEN followup_date_on = "'.$dateArray[6].'" THEN 1 ELSE null END) as countSat'),
				 ])
					  ->where('followup_status',0);
		
				$WFData->where('followup_branch_id',$centre);
				if($privilege == 48)
				{
					$WFData->where('followup_branch_id',$centre);
					$WFData->where('followup_assigned_users_id',$id);
				}
					//$WFData->groupBy('followup_lead_id');
					$WFData=$WFData->first();
		
		//data 
					$countSun = $WFData->countSun;
					$countMon = $WFData->countMon;
					$countTue = $WFData->countTue;
					$countWed = $WFData->countWed;
					$countThu = $WFData->countThu;
					$countFri = $WFData->countFri;
					$countSat = $WFData->countSat;
		//data
		$leadData = [
				'name' => 'Lead',
				'data' => [ $wData->countSun, $wData->countMon, $wData->countTue, $wData->countWed, $wData->countThu, $wData->countFri, $wData->countSat],
					];
		
		$followupData =  [
						'name' => 'Followup',
						'data' => [$countSun,$countMon,$countTue,$countWed,$countThu,$countFri,$countSat],
						];
		
		$studentData =  [
							'name' => 'Student',
							'data' => [ $wData->studCountSun, $wData->studCountMon, $wData->studCountTue, $wData->studCountWed, $wData->studCountThu, $wData->studCountFri, $wData->studCountSat],
					];
		
		$allData = array_merge($leadData['data'], $followupData['data'], $studentData['data']);
		// Find the maximum value
		$maxValue = max($allData);
		$minValue = 0;
		if($maxValue <= 1)
		{
			$minValue = 0.5;
		}

		return response()->json(['leadData' => $leadData,'followupData'=>$followupData,'studentData'=>$studentData,'minValue'=>$minValue,'maxValue'=>$maxValue]);
		
	}
	
	public function line_chart_weekly()
	{
			ini_set('memory_limit', '-1');
			$currentMonth = date('m'); 
			$currentYear = date('Y'); 
			
			$privilege = Auth::user()->previlage;
			$centre = session('application_branch');
			$id = Auth::user()->id;

			$firstDayOfMonth = new \DateTime("{$currentYear}-{$currentMonth}-01");
			$lastDayOfMonth = new \DateTime("last day of {$currentYear}-{$currentMonth}");

			$weeks = [];
			$weeksDate = [];
			$currentDate = clone $firstDayOfMonth;

			while ($currentDate <= $lastDayOfMonth) {
				$weekStart = $currentDate->format('Y-m-d');
				$weekEnd = $currentDate->modify('next Sunday')->format('Y-m-d');
				
			//	$weekStart1 = $currentDate->format('d M');
			//	$weekEnd1 = $currentDate->modify('next Sunday')->format('d M');
				
				if ($currentDate > $lastDayOfMonth) {
					$weekEnd = $lastDayOfMonth->format('Y-m-d');
					//$weekEnd1 = $lastDayOfMonth->format('d M');
				}

				$weeks[] = [
					'start' => $weekStart,
					'end' => $weekEnd,
				];
				$currentDate->modify('+1 day'); 
			}
		
		
		$data = DB::table('tbl_lead')
				->select(
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$weeks[0]['start'].'" AND lead_date_on <="'.$weeks[0]['end'].'" THEN 1 ELSE null END) countFW'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$weeks[1]['start'].'" AND lead_date_on <="'.$weeks[1]['end'].'" THEN 1 ELSE null END) countSW'),
				   DB::raw('COUNT(CASE WHEN lead_date_on >="'.$weeks[2]['start'].'" AND lead_date_on <="'.$weeks[2]['end'].'" THEN 1 ELSE null END) countTW'),
			 DB::raw('COUNT(CASE WHEN lead_date_on >="'.$weeks[3]['start'].'" AND lead_date_on <="'.$weeks[3]['end'].'" THEN 1 ELSE null END) countFTW'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$weeks[0]['start'].'" AND lead_date_on <="'.$weeks[0]['end'].'" THEN 1 ELSE null END) studentCountFW'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$weeks[1]['start'].'" AND lead_date_on <="'.$weeks[1]['end'].'" THEN 1 ELSE null END) studentCountSW'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$weeks[2]['start'].'" AND lead_date_on <="'.$weeks[2]['end'].'" THEN 1 ELSE null END) studentCountTW'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$weeks[3]['start'].'" AND lead_date_on <="'.$weeks[3]['end'].'" THEN 1 ELSE null END) studentCountFTW')
							 
			     )
				->where('lead_status',0);
		
		$data->where('lead_branch_id',$centre);
		if($privilege == 48)
		{
			$data->where('lead_branch_id',$centre);
			$data->where('lead_added_by',$id);
		}

		$data=$data->first();
			
			DB::enableQueryLog();
		
		$fData = DB::table('tbl_lead_followup')
					->select([
							'followup_lead_id',
						DB::raw('COUNT(CASE WHEN followup_date_on >="'.$weeks[0]['start'].'" AND followup_date_on <="'.$weeks[0]['end'].'" THEN 1 ELSE null END) as countFW'),
						DB::raw('COUNT(CASE WHEN followup_date_on >="'.$weeks[1]['start'].'" AND followup_date_on <="'.$weeks[1]['end'].'" THEN 1 ELSE null END) as countSW'),
						DB::raw('COUNT(CASE WHEN followup_date_on >="'.$weeks[2]['start'].'" AND followup_date_on <="'.$weeks[2]['end'].'" THEN 1 ELSE null END) as countTW'),
						DB::raw('COUNT(CASE WHEN followup_date_on >="'.$weeks[3]['start'].'" AND followup_date_on <="'.$weeks[3]['end'].'" THEN 1 ELSE null END) as countFTR')
						 ])
						->where('followup_status',0);
		
				$fData->where('followup_branch_id',$centre);
				
				if($privilege == 48)
				{
					$fData->where('followup_branch_id',$centre);
					$fData->where('followup_assigned_users_id',$id);
				}
					//$fData->groupBy('followup_lead_id');
				$fData=$fData->first();
		
		//data 
					$countFW = $fData->countFW;
					$countSW = $fData->countSW;
					$countTW = $fData->countTW;
					$countFTW = $fData->countFTR;
		//data
		
		
	   $leadData = [
				'name' => 'Lead',
				'data' => [ $data->countFW, $data->countSW, $data->countTW, $data->countFTW],
					];
		$followupData =  [
						'name' => 'Followup',
						'data' => [$countFW,$countSW,$countTW,$countFTW],
						];
		$studentData =  [
							'name' => 'Student',
							'data' => [ $data->studentCountFW, $data->studentCountSW, $data->studentCountTW, $data->studentCountFTW],
					    ];
		
//for graph x axis 

			$weeksDate = [];
			$currentDate = clone $firstDayOfMonth;
			while ($currentDate <= $lastDayOfMonth) {
				$weekStart = $currentDate->format('d M');
				$weekEnd = $currentDate->modify('next Sunday')->format('d M');

				if ($currentDate > $lastDayOfMonth) {
					$weekEnd = $lastDayOfMonth->format('d M');
				}

				$weeksDate[] = [
					'start' => $weekStart,
					'end' => $weekEnd,
				];
				$currentDate->modify('+1 day'); 
			}

		$category = [
						$weeksDate[0]['start'].'-'.$weeksDate[0]['end'],
						$weeksDate[1]['start'].'-'.$weeksDate[1]['end'],
						$weeksDate[2]['start'].'-'.$weeksDate[2]['end'],
						$weeksDate[3]['start'].'-'.$weeksDate[3]['end'],
					];

		
		$allData = array_merge($leadData['data'], $followupData['data'], $studentData['data']);
		// Find the maximum value
		$maxValue = max($allData);
		$minValue = 0;
		if($maxValue <= 1)
		{
			$minValue = 0.5;
		}
		
		return response()->json(['leadData' => $leadData,'category'=>$category,'followupData'=>$followupData,'studentData'=>$studentData,'minValue'=>$minValue,'maxValue'=>$maxValue]);
	}
	
	public function line_chart_monthly()
	{
		ini_set('memory_limit', '-1');
		$privilege = Auth::user()->previlage;
		$centre = session('application_branch');
		$id = Auth::user()->id;

		$currentYear = date('y'); 
		
		$MD1 = date('Y-01-01');
		$MED1 = date('Y-01-t');
		
		$MD2 = date('Y-02-01');
		$MED2 = date('Y-02-t');
		
		$MD3 = date('Y-03-01');
		$MED3 = date('Y-03-t');
		
		$MD4 = date('Y-04-01');
		$MED4 = date('Y-04-t');
		
		$MD5 = date('Y-05-01');
		$MED5 = date('Y-05-t');
		
		$MD6 = date('Y-06-01');
		$MED6 = date('Y-06-t');
		
		$MD7 = date('Y-07-01');
		$MED7 = date('Y-07-t');
		
		$MD8 = date('Y-08-01');
		$MED8 = date('Y-08-t');
		
		$MD9 = date('Y-09-01');
		$MED9 = date('Y-09-t');
		
		$MD10 = date('Y-10-01');
		$MED10 = date('Y-10-t');
		
		$MD11 = date('Y-11-01');
		$MED11 = date('Y-11-t');
		
		$MD12 = date('Y-12-01');
		$MED12 = date('Y-12-t');
		
		
		$data = DB::table('tbl_lead')
				->select(
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD1.'" AND lead_date_on <="'.$MED1.'" THEN 1 ELSE null END) count1M'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD2.'" AND lead_date_on <="'.$MED2.'" THEN 1 ELSE null END) count2M'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD3.'" AND lead_date_on <="'.$MED3.'" THEN 1 ELSE null END) count3M'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD4.'" AND lead_date_on <="'.$MED4.'" THEN 1 ELSE null END) count4M'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD5.'" AND lead_date_on <="'.$MED5.'" THEN 1 ELSE null END) count5M'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD6.'" AND lead_date_on <="'.$MED6.'" THEN 1 ELSE null END) count6M'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD7.'" AND lead_date_on <="'.$MED7.'" THEN 1 ELSE null END) count7M'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD8.'" AND lead_date_on <="'.$MED8.'" THEN 1 ELSE null END) count8M'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD9.'" AND lead_date_on <="'.$MED9.'" THEN 1 ELSE null END) count9M'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD10.'" AND lead_date_on <="'.$MED10.'" THEN 1 ELSE null END) count10M'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD11.'" AND lead_date_on <="'.$MED11.'" THEN 1 ELSE null END) count11M'),
					DB::raw('COUNT(CASE WHEN lead_date_on >="'.$MD12.'" AND lead_date_on <="'.$MED12.'" THEN 1 ELSE null END) count12M'),
					DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD1.'" AND lead_date_on <="'.$MED1.'" THEN 1 ELSE null END) studentCount1M'),
					DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD2.'" AND lead_date_on <="'.$MED2.'" THEN 1 ELSE null END) studentCount2M'),
					DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD3.'" AND lead_date_on <="'.$MED3.'" THEN 1 ELSE null END) studentCount3M'),
					DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD4.'" AND lead_date_on <="'.$MED4.'" THEN 1 ELSE null END) studentCount4M'),
					DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD5.'" AND lead_date_on <="'.$MED5.'" THEN 1 ELSE null END) studentCount5M'),
					DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD6.'" AND lead_date_on <="'.$MED6.'" THEN 1 ELSE null END) studentCount6M'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD7.'" AND lead_date_on <="'.$MED7.'" THEN 1 ELSE null END) studentCount7M'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD8.'" AND lead_date_on <="'.$MED8.'" THEN 1 ELSE null END) studentCount8M'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD9.'" AND lead_date_on <="'.$MED9.'" THEN 1 ELSE null END) studentCount9M'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD10.'" AND lead_date_on <="'.$MED10.'" THEN 1 ELSE null END) studentCount10M'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD11.'" AND lead_date_on <="'.$MED11.'" THEN 1 ELSE null END) studentCount11M'),
			DB::raw('COUNT(CASE WHEN lead_followup_type = 4 AND lead_date_on >="'.$MD12.'" AND lead_date_on <="'.$MED12.'" THEN 1 ELSE null END) studentCount12M')
					)
			  ->where('lead_status',0);
		
		$data->where('lead_branch_id',$centre);
		if($privilege == 48)
		{
			$data->where('lead_branch_id',$centre);
			$data->where('lead_added_by',$id);
		}
		
		$data=$data->first();
				
		//followup count
		
		$fData = DB::table('tbl_lead_followup')
				->select([
								'followup_lead_id',
					   DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD1.'" AND followup_date_on <="'.$MED1.'" THEN 1 ELSE null END) as count1M'),
					   DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD2.'" AND followup_date_on <="'.$MED1.'" THEN 1 ELSE null END) as count2M'),
					   DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD3.'" AND followup_date_on <="'.$MED3.'" THEN 1 ELSE null END) as count3M'),
					   DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD4.'" AND followup_date_on <="'.$MED4.'" THEN 1 ELSE null END) as count4M'),
					   DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD5.'" AND followup_date_on <="'.$MED5.'" THEN 1 ELSE null END) as count5M'),
					   DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD6.'" AND followup_date_on <="'.$MED6.'" THEN 1 ELSE null END) as count6M'), 
					   DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD7.'" AND followup_date_on <="'.$MED7.'" THEN 1 ELSE null END) as count7M'),  						DB::raw('MAX(CASE WHEN followup_date_on >="'.$MD8.'" AND followup_date_on <="'.$MED8.'" THEN 1 ELSE 0 END) as count8M'),
					   DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD9.'" AND followup_date_on <="'.$MED9.'" THEN 1 ELSE null END) as count9M'), 
				  DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD10.'" AND followup_date_on <="'.$MED10.'" THEN 1 ELSE null END) as count10M'),  
				  DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD11.'" AND followup_date_on <="'.$MED11.'" THEN 1 ELSE null END) as count11M'),
				  DB::raw('COUNT(CASE WHEN followup_date_on >="'.$MD12.'" AND followup_date_on <="'.$MED12.'" THEN 1 ELSE null END) as count12M')
								])

					->where('followup_status',0);
			$fData->where('followup_branch_id',$centre);
			if($privilege == 48)
			{
				//$fData->where('followup_branch_id',$centre);
				$fData->where('followup_assigned_users_id',$id);
			}
					//$fData->groupBy('followup_lead_id');
					$fData=$fData->first();
			
			//data 
					$count1M = $fData->count1M;
					$count2M = $fData->count2M;
					$count3M = $fData->count3M;
					$count4M = $fData->count4M;
					$count5M = $fData->count5M;
					$count6M = $fData->count6M;
					$count7M = $fData->count7M;
					$count8M = $fData->count8M;
					$count9M = $fData->count9M;
					$count10M = $fData->count10M;
					$count11M = $fData->count11M;
					$count12M = $fData->count12M;
			//data
		
		$leadData = [
				'name' => 'Lead',
				'data' => [ $data->count1M, $data->count2M, $data->count3M, $data->count4M,$data->count5M,$data->count6M,$data->count7M,$data->count8M,$data->count9M,$data->count10M,$data->count11M,$data->count12M],
					];
		$followupData =  [
						'name' => 'Followup',
						'data' => [$count1M,$count2M,$count3M,$count4M,$count5M,$count6M,$count7M,$count8M,$count9M,$count10M,$count11M,$count12M],
						];
		$studentData =  [
							'name' => 'Student',
							'data' => [ $data->studentCount1M, $data->studentCount2M, $data->studentCount3M, $data->studentCount4M,$data->studentCount5M,$data->studentCount6M,$data->studentCount7M,$data->studentCount8M,$data->studentCount9M,$data->studentCount10M,$data->studentCount11M,$data->studentCount12M],
					    ];
		
		$allData = array_merge($leadData['data'], $followupData['data'], $studentData['data']);

		// Find the maximum value
		$maxValue = max($allData);
		$minValue = 0;
		if($maxValue <= 1)
		{
			$minValue = 0.5;
		}

		$category = ['Jan '.$currentYear, 'Feb '.$currentYear, 'Mar '.$currentYear, 'Apr '.$currentYear, 'May '.$currentYear, 'Jun '.$currentYear, 'Jul '.$currentYear, 'Aug '.$currentYear, 'Sep '.$currentYear, 'Oct '.$currentYear, 'Nov '.$currentYear, 'Dec '.$currentYear];
		return response()->json(['leadData' => $leadData,'category'=>$category,'followupData'=>$followupData,'studentData'=>$studentData,'minValue'=>$minValue,'maxValue'=>$maxValue]);
	}
	
	public function coureLeadCount(Request $request)
	{
		$id  = $request->id;
		$privilege = Auth::user()->previlage;
		$userid = Auth::user()->id;
		//$centre = Auth::user()->user_branch;
		$centre = session('application_branch');
		$startDateString = date('Y-m-d');
		$endDateString = date('Y-m-d');
		
		$course = DB::table('tbl_course')
				->where('course_status',0)
				->orderBy('course_id','desc')	
				->take('10')
				->get();

		if($id == 1)
		{
			$q1 = 'COUNT(CASE WHEN lead_date_on ="'.$startDateString.'" THEN 1 ELSE null END)';
		}
		else if($id == 2)
		{
			$currentDate = new \DateTime();
			$startDate = clone $currentDate;
			$startDate->modify('last sunday');
			
			$endDate = clone $startDate;
			$endDate->modify('+6 days');
			
			$startDateString = $startDate->format('Y-m-d');
			$endDateString = $endDate->format('Y-m-d');
			$q1 = 'COUNT(CASE WHEN lead_date_on >="'.$startDateString.'" AND lead_date_on <="'.$endDateString.'" THEN 1 ELSE null END)';
		}
		else if($id == 3)
		{
			$startDateString = date('Y-m-01');
			$endDateString = date('Y-m-t');
			
			$q1 = 'COUNT(CASE WHEN lead_date_on >="'.$startDateString.'" AND lead_date_on <="'.$endDateString.'" THEN 1 ELSE null END)';
		}
			
			$totalLead = DB::table('tbl_lead')
						->select(DB::raw($q1.' totCount'))
						 ->where('lead_status',0);
				 $totalLead->where('lead_branch_id',$centre);
				 if($privilege == 48)
				 {
					 //$totalLead->where('lead_branch_id',$centre);
					  $totalLead->where('lead_added_by',$userid);
				 }

			$totalLead=$totalLead->first();
			
		return view('course_graph',compact('course','startDateString','endDateString','id','totalLead'));
	}
	
	
	public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')
            ->withSuccess('You have logged out successfully!');;
    }
}
